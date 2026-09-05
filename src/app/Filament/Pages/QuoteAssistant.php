<?php

namespace App\Filament\Pages;

use App\Contracts\AI\QuoteInterpreterInterface;
use App\Filament\Resources\Quotes\QuoteResource;
use App\Services\Quotes\QuoteBuilderService;
use App\Services\Quotes\QuoteInterpretationValidator;
use App\Services\Quotes\QuotePricingService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class QuoteAssistant extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Asistente presupuestos';

    protected static ?string $title = 'Asistente de presupuestos';

    protected static string|UnitEnum|null $navigationGroup = 'Presupuestos';

    protected string $view = 'filament.pages.quote-assistant';

    public ?array $data = [];

    public ?array $result = null;

    public function mount(): void
    {
        $this->form->fill([
            'request_text' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('request_text')
                    ->label('Solicitud del cliente')
                    ->placeholder(
                        'Ejemplo: Hola, necesito dos ventanas PVC blancas con doble cristal e instalación.'
                    )
                    ->rows(8)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function interpretRequest(
        QuoteInterpreterInterface $interpreter,
        QuoteInterpretationValidator $validator,
        QuotePricingService $pricingService,
    ): void {
        $data = $this->form->getState();

        try {
            $request = $interpreter->interpret(
                $data['request_text']
            );

            $validation = $validator->validate($request);

            $items = [];

            foreach ($validation['items'] as $item) {
                $pricing = $pricingService->calculate(
                    product: $item['product'],
                    quantity: $item['quantity'],
                    selectedOptions: $item['selected_options'],
                );

                $items[] = [
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_code' => $item['product']->sku,
                    'quantity' => $item['quantity'],

                    'options' => collect($item['selected_options'])
                        ->map(fn ($value) => [
                            'option_code' => $value->option->code,
                            'option_name' => $value->option->name,
                            'value_code' => $value->code,
                            'value_name' => $value->name,
                            'price_adjustment_type' => $value->price_adjustment_type,
                            'price_adjustment' => $value->price_adjustment,
                        ])
                        ->values()
                        ->all(),

                    'pricing' => $pricing,
                ];
            }

            $customer = $validation['customer'];

            $this->result = [
                'valid' => $validation['valid'],
                'complete' => $validation['complete'],

                'customer' => $customer ? [
                    'name' => $customer->name,
                    'companyName' => $customer->companyName,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'taxId' => $customer->taxId,
                    'address' => $customer->address,
                ] : null,

                'errors' => $validation['errors'],

                'missing_information' => $validation['missing_information'],

                'items' => $items,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            $this->result = null;

            Notification::make()
                ->title('No se ha podido interpretar la solicitud')
                ->body(
                    'Se ha producido un error al comunicarse con la IA. Inténtalo de nuevo.'
                )
                ->danger()
                ->send();
        }
    }

    public function saveQuote(
        QuoteBuilderService $quoteBuilder,
    ): void {
        if (! $this->result) {
            Notification::make()
                ->title('No hay ningún presupuesto para guardar')
                ->warning()
                ->send();

            return;
        }

        if (! $this->result['valid']) {
            Notification::make()
                ->title('El presupuesto contiene errores')
                ->body(
                    'Corrige los errores antes de guardar el presupuesto.'
                )
                ->danger()
                ->send();

            return;
        }

        if (! $this->result['complete']) {
            Notification::make()
                ->title('El presupuesto está incompleto')
                ->body(
                    'Falta información por confirmar antes de poder guardarlo.'
                )
                ->warning()
                ->send();

            return;
        }

        if (empty($this->result['items'])) {
            Notification::make()
                ->title('El presupuesto no contiene líneas')
                ->warning()
                ->send();

            return;
        }

        try {
            $quote = $quoteBuilder->createFromAssistant(
                result: $this->result,
                sourceText: $this->data['request_text'],
            );

            Notification::make()
                ->title('Presupuesto guardado')
                ->body(
                    "Se ha creado correctamente el presupuesto #{$quote->id}."
                )
                ->success()
                ->send();

            $this->redirect(
                QuoteResource::getUrl(
                    'edit',
                    [
                        'record' => $quote,
                    ]
                )
            );
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('No se ha podido guardar el presupuesto')
                ->body(
                    'Se ha producido un error al guardar el presupuesto.'
                )
                ->danger()
                ->send();
        }
    }
}