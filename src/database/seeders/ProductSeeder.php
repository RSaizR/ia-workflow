<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pvc = Product::create([
            'name' => 'Ventana PVC',
            'sku' => 'VENT-PVC',
            'description' => 'Ventana de PVC configurable.',
            'base_price' => 500,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $glass = $pvc->options()->create([
            'name' => 'Cristal',
            'code' => 'glass',
            'required' => true,
            'active' => true,
        ]);

        $glass->values()->createMany([
            [
                'name' => 'Normal',
                'code' => 'normal',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 0,
                'active' => true,
            ],
            [
                'name' => 'Doble',
                'code' => 'double',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 100,
                'active' => true,
            ],
            [
                'name' => 'Triple',
                'code' => 'triple',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 180,
                'active' => true,
            ],
            [
                'name' => 'Blindado',
                'code' => 'armored',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 350,
                'active' => true,
            ],
        ]);

        $color = $pvc->options()->create([
            'name' => 'Color',
            'code' => 'color',
            'required' => true,
            'active' => true,
        ]);

        $color->values()->createMany([
            [
                'name' => 'Blanco',
                'code' => 'white',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 0,
                'active' => true,
            ],
            [
                'name' => 'Negro',
                'code' => 'black',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 50,
                'active' => true,
            ],
            [
                'name' => 'Madera',
                'code' => 'wood',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 80,
                'active' => true,
            ],
        ]);

        $installation = $pvc->options()->create([
            'name' => 'Instalación',
            'code' => 'installation',
            'required' => true,
            'active' => true,
        ]);

        $installation->values()->createMany([
            [
                'name' => 'Sin instalación',
                'code' => 'without',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 0,
                'active' => true,
            ],
            [
                'name' => 'Con instalación',
                'code' => 'included',
                'price_adjustment_type' => 'fixed',
                'price_adjustment' => 150,
                'active' => true,
            ],
        ]);

        Product::create([
            'name' => 'Ventana aluminio',
            'sku' => 'VENT-ALU',
            'description' => 'Ventana de aluminio.',
            'base_price' => 600,
            'unit' => 'unidad',
            'active' => true,
        ]);

        Product::create([
            'name' => 'Mosquitera',
            'sku' => 'MOSQ',
            'description' => 'Mosquitera para ventana.',
            'base_price' => 90,
            'unit' => 'unidad',
            'active' => true,
        ]);

        Product::create([
            'name' => 'Reparación persiana',
            'sku' => 'REP-PERS',
            'description' => 'Servicio de reparación de persiana.',
            'base_price' => 120,
            'unit' => 'servicio',
            'active' => true,
        ]);

        Product::create([
            'name' => 'Instalación',
            'sku' => 'INST',
            'description' => 'Servicio de instalación.',
            'base_price' => 150,
            'unit' => 'servicio',
            'active' => true,
        ]);
    }
}