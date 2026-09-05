<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        CompanyProfile::create([
            'legal_name' => 'Ventanas Inteligentes S.L.',
            'commercial_name' => 'Ventanas Inteligentes',
            'tax_id' => 'B12345678',

            'address' => 'Calle Principal, 25',
            'postal_code' => '46001',
            'city' => 'Valencia',
            'province' => 'Valencia',
            'country' => 'España',

            'email' => 'administracion@ventanasinteligentes.es',
            'phone' => '960 000 000',
            'website' => 'https://ventanasinteligentes.es',

            'iban' => 'ES00 0000 0000 0000 0000 0000',

            'invoice_series' => 'FAC',

            'invoice_footer' => 'Gracias por confiar en nosotros.',

            'active' => true,
        ]);
    }
}