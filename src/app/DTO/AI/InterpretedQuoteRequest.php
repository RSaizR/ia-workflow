<?php

namespace App\DTO\AI;

final readonly class InterpretedQuoteRequest
{
    public function __construct(
        public ?InterpretedCustomer $customer,
        public array $items,
        public array $missingInformation = [],
    ) {
    }
}