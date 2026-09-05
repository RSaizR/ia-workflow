<?php

namespace App\Filament\Resources\Quotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        'sent' => 'Enviado',
                    ])
                    ->default('draft')
                    ->required(),

                Select::make('source')
                    ->label('Origen')
                    ->options([
                        'manual' => 'Manual',
                        'ai' => 'Inteligencia artificial',
                        'email' => 'Correo electrónico',
                    ])
                    ->default('manual')
                    ->required(),

                DatePicker::make('valid_until')
                    ->label('Válido hasta')
                    ->displayFormat('d/m/Y'),

                Repeater::make('items')
                    ->label('Líneas del presupuesto')
                    ->relationship()
                    ->schema([

                        Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('description')
                            ->label('Descripción')
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->required(),

                        TextInput::make('unit_price')
                            ->label('Precio unitario')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('€')
                            ->required(),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('€')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->addActionLabel('Añadir línea'),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->suffix('€')
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('tax_rate')
                    ->label('IVA')
                    ->numeric()
                    ->suffix('%')
                    ->default(21)
                    ->required(),

                TextInput::make('tax')
                    ->label('Importe IVA')
                    ->numeric()
                    ->suffix('€')
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->suffix('€')
                    ->disabled()
                    ->dehydrated(),

                Textarea::make('notes')
                    ->label('Observaciones')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('source_text')
                    ->label('Solicitud original')
                    ->rows(5)
                    ->columnSpanFull(),

                TextInput::make('generated_email_subject')
                    ->label('Asunto del correo')
                    ->columnSpanFull(),

                Textarea::make('generated_email_body')
                    ->label('Contenido del correo')
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}