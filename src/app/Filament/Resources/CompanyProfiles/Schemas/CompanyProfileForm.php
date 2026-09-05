<?php

namespace App\Filament\Resources\CompanyProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('legal_name')
                    ->label('Razón social')
                    ->required()
                    ->maxLength(255),

                TextInput::make('commercial_name')
                    ->label('Nombre comercial')
                    ->maxLength(255),

                TextInput::make('tax_id')
                    ->label('NIF / CIF')
                    ->required()
                    ->maxLength(50),

                TextInput::make('address')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(255),

                TextInput::make('postal_code')
                    ->label('Código postal')
                    ->maxLength(20),

                TextInput::make('city')
                    ->label('Localidad')
                    ->maxLength(100),

                TextInput::make('province')
                    ->label('Provincia')
                    ->maxLength(100),

                TextInput::make('country')
                    ->label('País')
                    ->default('España')
                    ->required()
                    ->maxLength(100),

                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('website')
                    ->label('Página web')
                    ->url()
                    ->maxLength(255),

                TextInput::make('iban')
                    ->label('IBAN')
                    ->maxLength(50),

                TextInput::make('invoice_series')
                    ->label('Serie de facturación')
                    ->helperText('Ejemplo: FAC')
                    ->default('FAC')
                    ->required()
                    ->maxLength(20),

                FileUpload::make('logo_path')
                    ->label('Logotipo')
                    ->image()
                    ->disk('public')
                    ->directory('company')
                    ->imageEditor(),

                Textarea::make('invoice_footer')
                    ->label('Pie de factura')
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('active')
                    ->label('Empresa activa')
                    ->default(true),
            ]);
    }
}