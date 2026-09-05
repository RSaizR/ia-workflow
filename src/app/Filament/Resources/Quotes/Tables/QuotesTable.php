<?php

namespace App\Filament\Resources\Quotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('N.º')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Sin cliente'),

                TextColumn::make('status')
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

                TextColumn::make('source')
                    ->label('Origen')
                    ->formatStateUsing(
                        fn (?string $state): string => match ($state) {
                            'manual' => 'Manual',
                            'ai' => 'Inteligencia artificial',
                            'email' => 'Correo electrónico',
                            default => $state ?? '-',
                        }
                    ),

                TextColumn::make('total')
                    ->label('Total')
                    ->money(
                        'EUR',
                        locale: 'es'
                    )
                    ->sortable(),

                TextColumn::make('invoice.invoice_number')
                    ->label('Factura')
                    ->placeholder('Sin generar'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Editar'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(
                            'Eliminar seleccionados'
                        ),
                ]),
            ]);
    }
}