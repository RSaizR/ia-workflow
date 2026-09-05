<?php

namespace App\Services\Invoices;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function createFromQuote(Quote $quote): Invoice
    {
        return DB::transaction(function () use ($quote) {
            $quote->load([
                'customer',
                'items',
                'invoice',
            ]);

            if ($quote->invoice) {
                return $quote->invoice->load('items');
            }

            if ($quote->items->isEmpty()) {
                throw new RuntimeException(
                    'El presupuesto no contiene líneas.'
                );
            }

            $company = CompanyProfile::query()
                ->where('active', true)
                ->first();

            if (! $company) {
                throw new RuntimeException(
                    'No existe ninguna empresa activa configurada.'
                );
            }

            $customer = $quote->customer;

            if (! $customer) {
                throw new RuntimeException(
                    'El presupuesto no tiene ningún cliente asociado.'
                );
            }

            if (! $customer->name) {
                throw new RuntimeException(
                    'El cliente debe tener un nombre.'
                );
            }

            if (! $customer->tax_id) {
                throw new RuntimeException(
                    'El cliente debe tener NIF o CIF antes de generar la factura.'
                );
            }

            if (! $customer->address) {
                throw new RuntimeException(
                    'El cliente debe tener una dirección antes de generar la factura.'
                );
            }

            if (! $company->legal_name) {
                throw new RuntimeException(
                    'La empresa debe tener una razón social.'
                );
            }

            if (! $company->tax_id) {
                throw new RuntimeException(
                    'La empresa debe tener NIF o CIF.'
                );
            }

            if (! $company->address) {
                throw new RuntimeException(
                    'La empresa debe tener una dirección.'
                );
            }

            $series = $company->invoice_series ?: 'FAC';

            /*
             * Bloqueamos los registros de la serie para evitar
             * que dos facturas obtengan el mismo número.
             */
            $lastSequence = Invoice::query()
                ->where('series', $series)
                ->lockForUpdate()
                ->max('sequence');

            $sequence = ((int) $lastSequence) + 1;

            $invoiceNumber = sprintf(
                '%s-%s-%06d',
                $series,
                now()->format('Y'),
                $sequence
            );

            $subtotal = $quote->items->sum(
                fn ($item) => (float) $item->subtotal
            );

            $taxRate = (float) (
                $quote->tax_rate
                ?: config('quotes.tax_rate', 21)
            );

            $tax = round(
                $subtotal * ($taxRate / 100),
                2
            );

            $total = round(
                $subtotal + $tax,
                2
            );

            $invoice = Invoice::create([
                'quote_id' => $quote->id,

                'series' => $series,
                'sequence' => $sequence,
                'invoice_number' => $invoiceNumber,

                'issued_at' => now()->toDateString(),

                'issuer_legal_name' => $company->legal_name,
                'issuer_commercial_name' => $company->commercial_name,
                'issuer_tax_id' => $company->tax_id,
                'issuer_address' => $company->address,
                'issuer_postal_code' => $company->postal_code,
                'issuer_city' => $company->city,
                'issuer_province' => $company->province,
                'issuer_country' => $company->country,
                'issuer_email' => $company->email,
                'issuer_phone' => $company->phone,
                'issuer_website' => $company->website,
                'issuer_iban' => $company->iban,

                'customer_name' => $customer->name,
                'customer_company_name' => $customer->company_name,
                'customer_tax_id' => $customer->tax_id,
                'customer_address' => $customer->address,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,

                'subtotal' => round($subtotal, 2),
                'tax_rate' => $taxRate,
                'tax' => $tax,
                'total' => $total,

                'notes' => $quote->notes,
                'footer' => $company->invoice_footer,

                'status' => 'issued',
            ]);

            foreach ($quote->items as $quoteItem) {
                $invoice->items()->create([
                    'description' => $quoteItem->description,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->unit_price,
                    'subtotal' => $quoteItem->subtotal,
                    'metadata' => $quoteItem->metadata,
                ]);
            }

            return $invoice->load('items');
        });
    }
}