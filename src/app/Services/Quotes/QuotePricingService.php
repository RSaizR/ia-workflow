<?php

namespace App\Services\Quotes;

use App\Models\Product;
use App\Models\ProductOptionValue;
use InvalidArgumentException;

class QuotePricingService
{
    public function calculate(
        Product $product,
        float $quantity,
        array $selectedOptions = [],
        float $taxRate = 21
    ): array {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        $basePrice = (float) $product->base_price;

        $supplements = 0;

        foreach ($selectedOptions as $optionValue) {
            if (! $optionValue instanceof ProductOptionValue) {
                throw new InvalidArgumentException(
                    'All selected options must be ProductOptionValue instances.'
                );
            }

            if (! $optionValue->active) {
                throw new InvalidArgumentException(
                    "Option value {$optionValue->id} is inactive."
                );
            }

            if ($optionValue->option->product_id !== $product->id) {
                throw new InvalidArgumentException(
                    "Option value {$optionValue->id} does not belong to product {$product->id}."
                );
            }

            $adjustment = (float) $optionValue->price_adjustment;

            switch ($optionValue->price_adjustment_type) {
                case 'fixed':
                    $supplements += $adjustment;
                    break;

                case 'percentage':
                    $supplements += $basePrice * ($adjustment / 100);
                    break;

                default:
                    throw new InvalidArgumentException(
                        "Unsupported price adjustment type: {$optionValue->price_adjustment_type}"
                    );
            }
        }

        $unitPrice = $basePrice + $supplements;
        $subtotal = $unitPrice * $quantity;
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        return [
            'base_amount' => round($basePrice, 2),
            'supplements' => round($supplements, 2),
            'unit_price' => round($unitPrice, 2),
            'quantity' => $quantity,
            'subtotal' => round($subtotal, 2),
            'tax_rate' => $taxRate,
            'tax' => round($tax, 2),
            'total' => round($total, 2),
        ];
    }
}