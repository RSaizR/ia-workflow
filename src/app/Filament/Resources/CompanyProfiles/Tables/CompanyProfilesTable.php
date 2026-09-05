<?php

namespace App\Filament\Resources\CompanyProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('legal_name')
                    ->label('Razón social')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('commercial_name')
                    ->label('Nombre comercial')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('tax_id')
                    ->label('NIF / CIF')
                    ->searchable(),

                TextColumn::make('city')
                    ->label('Localidad')
                    ->placeholder('-'),

                TextColumn::make('invoice_series')
                    ->label('Serie facturas'),

                IconColumn::make('active')
                    ->label('Activa')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Última modificación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}