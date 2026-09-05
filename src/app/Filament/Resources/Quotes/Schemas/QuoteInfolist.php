<?php

namespace App\Filament\Resources\Quotes\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Presupuesto')
                    ->schema([
                        TextEntry::make('id')
                            ->label('N.º presupuesto'),

                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'draft' => 'Borrador',
                                    'pending' => 'Pendiente',
                                    'approved' => 'Aprobado',
                                    'rejected' => 'Rechazado',
                                    'sent' => 'Enviado',
                                    default => $state ?? '-',
                                }
                            ),

                        TextEntry::make('created_at')
                            ->label('Fecha')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('source')
                            ->label('Origen')
                            ->formatStateUsing(
                                fn (?string $state): string => match ($state) {
                                    'manual' => 'Manual',
                                    'ai' => 'Inteligencia artificial',
                                    'email' => 'Correo electrónico',
                                    default => $state ?? '-',
                                }
                            ),
                    ])
                    ->columns(4),

                Section::make('Cliente')
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label('Nombre')
                            ->placeholder('-'),

                        TextEntry::make('customer.company_name')
                            ->label('Empresa')
                            ->placeholder('-'),

                        TextEntry::make('customer.tax_id')
                            ->label('NIF / CIF')
                            ->placeholder('-'),

                        TextEntry::make('customer.email')
                            ->label('Correo electrónico')
                            ->placeholder('-'),

                        TextEntry::make('customer.phone')
                            ->label('Teléfono')
                            ->placeholder('-'),

                        TextEntry::make('customer.address')
                            ->label('Dirección')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Líneas')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Concepto'),

                                TextEntry::make('quantity')
                                    ->label('Cantidad'),

                                TextEntry::make('unit_price')
                                    ->label('Precio unitario')
                                    ->money(
                                        'EUR',
                                        locale: 'es'
                                    ),

                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money(
                                        'EUR',
                                        locale: 'es'
                                    ),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Totales')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Base imponible')
                            ->money(
                                'EUR',
                                locale: 'es'
                            ),

                        TextEntry::make('tax_rate')
                            ->label('IVA')
                            ->suffix(' %'),

                        TextEntry::make('tax')
                            ->label('IVA')
                            ->money(
                                'EUR',
                                locale: 'es'
                            ),

                        TextEntry::make('total')
                            ->label('Total')
                            ->money(
                                'EUR',
                                locale: 'es'
                            ),
                    ])
                    ->columns(4),

                Section::make('Factura')
                    ->schema([
                        TextEntry::make('invoice.invoice_number')
                            ->label('Número de factura')
                            ->placeholder('Todavía no generada'),

                        TextEntry::make('invoice.issued_at')
                            ->label('Fecha de emisión')
                            ->date('d/m/Y')
                            ->placeholder('-'),

                        TextEntry::make('invoice.total')
                            ->label('Total factura')
                            ->money(
                                'EUR',
                                locale: 'es'
                            )
                            ->placeholder('-'),
                    ])
                    ->columns(3),
            ]);
    }
}