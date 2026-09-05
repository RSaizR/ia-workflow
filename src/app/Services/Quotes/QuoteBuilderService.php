<?php

namespace App\Services\Quotes;

use App\Models\Customer;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class QuoteBuilderService
{
    public function createFromAssistant(array $result, string $sourceText): Quote
    {
        return DB::transaction(function () use ($result, $sourceText) {
            $customer = $this->resolveCustomer(
                $result['customer'] ?? null
            );

            $subtotal = collect($result['items'])
                ->sum(
                    fn (array $item) => (float) $item['pricing']['subtotal']
                );

            $tax = collect($result['items'])
                ->sum(
                    fn (array $item) => (float) $item['pricing']['tax']
                );

            $total = collect($result['items'])
                ->sum(
                    fn (array $item) => (float) $item['pricing']['total']
                );

            $quote = Quote::create([
                'customer_id' => $customer?->id,
                'status' => 'draft',
                'subtotal' => round($subtotal, 2),
                'tax_rate' => (float) config('quotes.tax_rate', 21),
                'tax' => round($tax, 2),
                'total' => round($total, 2),
                'source' => 'ai',
                'source_text' => $sourceText,
            ]);

            foreach ($result['items'] as $item) {
                $quote->items()->create([
                    'product_id' => $item['product_id'],
                    'description' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['pricing']['unit_price'],
                    'subtotal' => $item['pricing']['subtotal'],

                    'metadata' => [
                        'product_code' => $item['product_code'],
                        'options' => $item['options'],
                    ],
                ]);
            }

            return $quote->load([
                'customer',
                'items.product',
            ]);
        });
    }

    private function resolveCustomer(?array $customerData): ?Customer
    {
        if (
            ! $customerData
            || empty($customerData['name'])
        ) {
            return null;
        }

        if (! empty($customerData['email'])) {
            return Customer::updateOrCreate(
                [
                    'email' => $customerData['email'],
                ],
                [
                    'name' => $customerData['name'],
                    'company_name' => $customerData['companyName'] ?? null,
                    'phone' => $customerData['phone'] ?? null,
                    'tax_id' => $customerData['taxId'] ?? null,
                    'address' => $customerData['address'] ?? null,
                ],
            );
        }

        return Customer::create([
            'name' => $customerData['name'],
            'company_name' => $customerData['companyName'] ?? null,
            'email' => null,
            'phone' => $customerData['phone'] ?? null,
            'tax_id' => $customerData['taxId'] ?? null,
            'address' => $customerData['address'] ?? null,
        ]);
    }
}