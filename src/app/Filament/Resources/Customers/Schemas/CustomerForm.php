<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('company_name')
                    ->label('Empresa')
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('tax_id')
                    ->label('NIF / CIF')
                    ->maxLength(50),

                Textarea::make('address')
                    ->label('Dirección')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Observaciones')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}