<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name' => 'Juan García',
            'company_name' => null,
            'email' => 'juan.garcia@example.com',
            'phone' => '600123456',
            'tax_id' => '12345678Z',
            'address' => 'C/ Mayor, 15, Valencia',
            'notes' => null,
        ]);

        Customer::create([
            'name' => 'María López',
            'company_name' => 'Reformas López SL',
            'email' => 'maria.lopez@example.com',
            'phone' => '611234567',
            'tax_id' => 'B12345678',
            'address' => 'Av. del Puerto, 42, Valencia',
            'notes' => 'Cliente habitual.',
        ]);

        Customer::create([
            'name' => 'Carlos Martínez',
            'company_name' => null,
            'email' => 'carlos.martinez@example.com',
            'phone' => '622345678',
            'tax_id' => '87654321X',
            'address' => 'C/ Colón, 24, Valencia',
            'notes' => null,
        ]);
    }
}