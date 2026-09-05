<?php

namespace App\Contracts\AI;

use App\DTO\AI\InterpretedQuoteRequest;

interface QuoteInterpreterInterface
{
    public function interpret(string $text): InterpretedQuoteRequest;
}