<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sku')
                    ->label('Código / SKU')
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('base_price')
                    ->label('Precio base')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->suffix('€')
                    ->required(),

                TextInput::make('unit')
                    ->label('Unidad')
                    ->required()
                    ->maxLength(50),

                Toggle::make('active')
                    ->label('Activo')
                    ->default(true),

                Repeater::make('options')
                    ->label('Opciones del producto')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        TextInput::make('code')
                            ->label('Código')
                            ->required(),

                        Toggle::make('required')
                            ->label('Obligatoria'),

                        Toggle::make('active')
                            ->label('Activa')
                            ->default(true),

                        Repeater::make('values')
                            ->label('Valores disponibles')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),

                                TextInput::make('code')
                                    ->label('Código')
                                    ->required(),

                                Select::make('price_adjustment_type')
                                    ->label('Tipo de ajuste')
                                    ->options([
                                        'fixed' => 'Importe fijo',
                                        'percentage' => 'Porcentaje',
                                    ])
                                    ->default('fixed')
                                    ->required(),

                                TextInput::make('price_adjustment')
                                    ->label('Ajuste de precio')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->required(),

                                Toggle::make('active')
                                    ->label('Activo')
                                    ->default(true),
                            ])
                            ->addActionLabel('Añadir valor')
                            ->columns(2),
                    ])
                    ->addActionLabel('Añadir opción')
                    ->columnSpanFull(),
            ]);
    }
}