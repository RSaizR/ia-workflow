<?php

namespace App\DTO\AI;

final readonly class InterpretedQuoteItem
{
    public function __construct(
        public string $productCode,
        public float $quantity,
        public array $options = [],
    ) {
    }
}