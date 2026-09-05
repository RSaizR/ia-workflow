<?php

namespace App\Services\Quotes;

use App\DTO\AI\InterpretedQuoteRequest;
use App\Models\Product;

class QuoteInterpretationValidator
{
    public function validate(InterpretedQuoteRequest $request): array
    {
        $validatedItems = [];
        $errors = [];
        $missingInformation = $request->missingInformation;

        foreach ($request->items as $index => $item) {
            $product = Product::query()
                ->where('sku', $item->productCode)
                ->where('active', true)
                ->with([
                    'options' => fn ($query) => $query
                        ->where('active', true),

                    'options.values' => fn ($query) => $query
                        ->where('active', true),
                ])
                ->first();

            if (! $product) {
                $errors[] = [
                    'item' => $index,
                    'field' => 'product',
                    'message' => "El producto {$item->productCode} no existe o está inactivo.",
                ];

                continue;
            }

            if ($item->quantity <= 0) {
                $errors[] = [
                    'item' => $index,
                    'field' => 'quantity',
                    'message' => 'La cantidad debe ser mayor que cero.',
                ];

                continue;
            }

            $selectedOptionValues = [];

            foreach ($item->options as $optionCode => $valueCode) {
                $option = $product->options->firstWhere(
                    'code',
                    $optionCode
                );

                if (! $option) {
                    $errors[] = [
                        'item' => $index,
                        'field' => $optionCode,
                        'message' => "La opción {$optionCode} no existe para {$product->name}.",
                    ];

                    continue;
                }

                $value = $option->values->firstWhere(
                    'code',
                    $valueCode
                );

                if (! $value) {
                    $errors[] = [
                        'item' => $index,
                        'field' => $optionCode,
                        'message' => "El valor {$valueCode} no existe para la opción {$option->name}.",
                    ];

                    continue;
                }

                $selectedOptionValues[] = $value;
            }

            foreach ($product->options->where('required', true) as $requiredOption) {
                if (! array_key_exists(
                    $requiredOption->code,
                    $item->options
                )) {
                    $missingInformation[] = [
                        'item' => $index,
                        'field' => $requiredOption->code,
                        'message' => "Falta indicar {$requiredOption->name}.",
                    ];
                }
            }

            $validatedItems[] = [
                'product' => $product,
                'quantity' => $item->quantity,
                'selected_options' => $selectedOptionValues,
            ];
        }

        return [
            'valid' => empty($errors),
            'complete' => empty($errors) && empty($missingInformation),
            'errors' => $errors,
            'missing_information' => $missingInformation,
            'items' => $validatedItems,
            'customer' => $request->customer,
        ];
    }
}