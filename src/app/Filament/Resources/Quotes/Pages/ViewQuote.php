<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Invoice;
use App\Services\Invoices\InvoicePdfService;
use App\Services\Invoices\InvoiceService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    public function getTitle(): string
    {
        return "Presupuesto #{$this->record->id}";
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar presupuesto'),

            Action::make('generateInvoice')
                ->label('Generar factura')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generar factura')
                ->modalDescription(
                    'Se generará una factura definitiva utilizando los datos actuales del presupuesto, cliente y empresa.'
                )
                ->modalSubmitActionLabel('Generar factura')
                ->modalCancelActionLabel('Cancelar')
                ->visible(
                    fn (): bool => ! $this->record
                        ->invoice()
                        ->exists()
                )
                ->action(function (
                    InvoiceService $invoiceService
                ): void {
                    try {
                        $invoice = $invoiceService
                            ->createFromQuote(
                                $this->record
                            );

                        $this->record->refresh();

                        Notification::make()
                            ->title('Factura generada')
                            ->body(
                                "Se ha generado la factura {$invoice->invoice_number}."
                            )
                            ->success()
                            ->send();
                    } catch (\Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('No se ha podido generar la factura')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('downloadInvoice')
                ->label('Descargar factura PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(
                    fn (): bool => $this->record
                        ->invoice()
                        ->exists()
                )
                ->action(function (
                    InvoicePdfService $pdfService
                ): ?StreamedResponse {
                    $invoice = $this->record
                        ->invoice()
                        ->with('items')
                        ->first();

                    if (! $invoice instanceof Invoice) {
                        Notification::make()
                            ->title('Factura no encontrada')
                            ->danger()
                            ->send();

                        return null;
                    }

                    $pdf = $pdfService->generate(
                        $invoice
                    );

                    return response()->streamDownload(
                        function () use ($pdf): void {
                            echo $pdf->output();
                        },
                        $pdfService->filename(
                            $invoice
                        ),
                        [
                            'Content-Type' => 'application/pdf',
                        ]
                    );
                }),
        ];
    }
}