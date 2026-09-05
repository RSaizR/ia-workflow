<?php

namespace App\Services\AI;

use App\Models\Product;

class QuoteCatalogContextBuilder
{
    public function build(): array
    {
        return Product::query()
            ->where('active', true)
            ->with([
                'options' => fn ($query) => $query
                    ->where('active', true)
                    ->orderBy('id'),

                'options.values' => fn ($query) => $query
                    ->where('active', true)
                    ->orderBy('id'),
            ])
            ->orderBy('id')
            ->get()
            ->map(function (Product $product) {
                return [
                    'code' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,

                    'options' => $product->options
                        ->map(function ($option) {
                            return [
                                'code' => $option->code,
                                'name' => $option->name,
                                'required' => (bool) $option->required,

                                'values' => $option->values
                                    ->map(function ($value) {
                                        return [
                                            'code' => $value->code,
                                            'name' => $value->name,
                                        ];
                                    })
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }
}