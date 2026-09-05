<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    public function getTitle(): string
    {
        return 'Editar presupuesto';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar presupuesto'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $subtotal = collect($data['items'] ?? [])
            ->sum(function (array $item): float {
                $quantity = (float) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                return $quantity * $unitPrice;
            });

        $taxRate = (float) (
            $data['tax_rate']
            ?? config('quotes.tax_rate', 21)
        );

        $tax = $subtotal * ($taxRate / 100);

        $data['subtotal'] = round($subtotal, 2);
        $data['tax'] = round($tax, 2);
        $data['total'] = round(
            $subtotal + $tax,
            2
        );

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->record->items as $item) {
            $item->update([
                'subtotal' => round(
                    (float) $item->quantity
                    * (float) $item->unit_price,
                    2
                ),
            ]);
        }

        $subtotal = $this->record
            ->items()
            ->sum('subtotal');

        $taxRate = (float) $this->record->tax_rate;

        $tax = $subtotal * ($taxRate / 100);

        $this->record->updateQuietly([
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round(
                $subtotal + $tax,
                2
            ),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl(
            'edit',
            [
                'record' => $this->record,
            ]
        );
    }
}