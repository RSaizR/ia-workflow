<?php

namespace App\Services\AI;

use App\Contracts\AI\QuoteInterpreterInterface;
use App\DTO\AI\InterpretedCustomer;
use App\DTO\AI\InterpretedQuoteItem;
use App\DTO\AI\InterpretedQuoteRequest;

class FakeQuoteInterpreter implements QuoteInterpreterInterface
{
    public function interpret(string $text): InterpretedQuoteRequest
    {
        return new InterpretedQuoteRequest(
            customer: new InterpretedCustomer(
                name: 'Pedro Martínez',
                companyName: 'Reformas Martínez',
                email: 'pedro@example.com',
            ),

            items: [
                new InterpretedQuoteItem(
                    productCode: 'VENT-PVC',
                    quantity: 2,
                    options: [
                        'glass' => 'double',
                        'color' => 'white',
                        'installation' => 'included',
                    ],
                ),
            ],

            missingInformation: [],
        );
    }
}