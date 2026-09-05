<?php

namespace App\DTO\AI;

final readonly class InterpretedCustomer
{
    public function __construct(
        public ?string $name = null,
        public ?string $companyName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $taxId = null,
    ) {
    }
}