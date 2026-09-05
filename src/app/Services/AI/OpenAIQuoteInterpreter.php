<?php

namespace App\Services\AI;

use App\Contracts\AI\QuoteInterpreterInterface;
use App\DTO\AI\InterpretedCustomer;
use App\DTO\AI\InterpretedQuoteItem;
use App\DTO\AI\InterpretedQuoteRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIQuoteInterpreter implements QuoteInterpreterInterface
{
    public function __construct(
        private QuoteCatalogContextBuilder $catalogContextBuilder,
    ) {
    }

    public function interpret(string $text): InterpretedQuoteRequest
    {
        $catalog = $this->catalogContextBuilder->build();

        $response = Http::withToken(
            config('services.openai.api_key')
        )
            ->timeout(30)
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-5.6-luna'),

                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->systemPrompt($catalog),
                            ],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $text,
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Error comunicando con OpenAI: ' . $response->body()
            );
        }

        $outputText = $this->extractOutputText(
            $response->json()
        );

        $data = json_decode(
            $outputText,
            true
        );

        if (! is_array($data)) {
            throw new RuntimeException(
                'OpenAI no ha devuelto un JSON válido.'
            );
        }

        return $this->mapToDto($data);
    }

    private function systemPrompt(array $catalog): string
    {
        $catalogJson = json_encode(
            $catalog,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        return <<<PROMPT
Eres un asistente que interpreta solicitudes de clientes para preparar presupuestos.

Tu única responsabilidad es transformar el texto del cliente en información estructurada.

REGLAS IMPORTANTES:

1. No calcules precios.
2. No inventes productos.
3. Solo puedes utilizar productos presentes en el catálogo.
4. Solo puedes utilizar códigos de producto presentes en el catálogo.
5. Solo puedes utilizar opciones y valores presentes en el catálogo.
6. Si una información necesaria no aparece en el texto, no la inventes.
7. Añade a "missing_information" cualquier información necesaria para interpretar correctamente la solicitud que no esté disponible o sea ambigua.
8. Si el cliente proporciona una dirección, inclúyela en "customer.address".
9. No añadas a "missing_information" información que ya pueda almacenarse en alguno de los campos disponibles del cliente.
10. Si el cliente solicita algo que podría corresponder a un producto del catálogo pero no existe suficiente información para determinarlo con seguridad, no inventes el producto y explica la ambigüedad en "missing_information".
11. Devuelve exclusivamente JSON válido.
12. No añadas Markdown.
13. No añadas explicaciones antes ni después del JSON.

CATÁLOGO DISPONIBLE:

{$catalogJson}

FORMATO DE RESPUESTA OBLIGATORIO:

{
    "customer": {
        "name": null,
        "company_name": null,
        "email": null,
        "phone": null,
        "tax_id": null,
        "address": null
    },
    "items": [
        {
            "product_code": "CODIGO_PRODUCTO",
            "quantity": 1,
            "options": {
                "codigo_opcion": "codigo_valor"
            }
        }
    ],
    "missing_information": []
}

Si no puedes determinar algún dato del cliente, utiliza null en ese campo.

Si no puedes identificar ningún producto con suficiente seguridad, devuelve:

"items": []

y explica qué información falta o qué parte de la solicitud es ambigua dentro de "missing_information".
PROMPT;
    }

    private function extractOutputText(array $response): string
    {
        foreach ($response['output'] ?? [] as $output) {
            foreach ($output['content'] ?? [] as $content) {
                if (
                    ($content['type'] ?? null) === 'output_text'
                    && isset($content['text'])
                ) {
                    return $content['text'];
                }
            }
        }

        throw new RuntimeException(
            'No se ha encontrado texto en la respuesta de OpenAI.'
        );
    }

    private function mapToDto(array $data): InterpretedQuoteRequest
    {
        $customerData = $data['customer'] ?? null;

        $customer = null;

        if (is_array($customerData)) {
            $customer = new InterpretedCustomer(
                name: $customerData['name'] ?? null,
                companyName: $customerData['company_name'] ?? null,
                email: $customerData['email'] ?? null,
                phone: $customerData['phone'] ?? null,
                taxId: $customerData['tax_id'] ?? null,
                address: $customerData['address'] ?? null,
            );
        }

        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $items[] = new InterpretedQuoteItem(
                productCode: $item['product_code'] ?? '',
                quantity: (float) ($item['quantity'] ?? 1),
                options: is_array($item['options'] ?? null)
                    ? $item['options']
                    : [],
            );
        }

        return new InterpretedQuoteRequest(
            customer: $customer,
            items: $items,
            missingInformation: is_array($data['missing_information'] ?? null)
                ? $data['missing_information']
                : [],
        );
    }
}