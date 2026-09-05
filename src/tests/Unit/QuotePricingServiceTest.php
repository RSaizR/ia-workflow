<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Services\Quotes\QuotePricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class QuotePricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuotePricingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new QuotePricingService();
    }

    public function test_it_calculates_product_without_options(): void
    {
        $product = Product::create([
            'name' => 'Mosquitera',
            'sku' => 'MOSQ',
            'base_price' => 100,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $result = $this->service->calculate(
            product: $product,
            quantity: 2,
            selectedOptions: [],
            taxRate: 21
        );

        $this->assertSame(100.0, $result['base_amount']);
        $this->assertSame(0.0, $result['supplements']);
        $this->assertSame(100.0, $result['unit_price']);
        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(42.0, $result['tax']);
        $this->assertSame(242.0, $result['total']);
    }

    public function test_it_calculates_product_with_fixed_options(): void
    {
        $product = Product::create([
            'name' => 'Ventana PVC',
            'sku' => 'VENT-PVC',
            'base_price' => 500,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $glassOption = ProductOption::create([
            'product_id' => $product->id,
            'name' => 'Cristal',
            'code' => 'glass',
            'required' => true,
            'active' => true,
        ]);

        $installationOption = ProductOption::create([
            'product_id' => $product->id,
            'name' => 'Instalación',
            'code' => 'installation',
            'required' => true,
            'active' => true,
        ]);

        $doubleGlass = ProductOptionValue::create([
            'product_option_id' => $glassOption->id,
            'name' => 'Doble',
            'code' => 'double',
            'price_adjustment_type' => 'fixed',
            'price_adjustment' => 100,
            'active' => true,
        ]);

        $installation = ProductOptionValue::create([
            'product_option_id' => $installationOption->id,
            'name' => 'Con instalación',
            'code' => 'included',
            'price_adjustment_type' => 'fixed',
            'price_adjustment' => 150,
            'active' => true,
        ]);

        $result = $this->service->calculate(
            product: $product,
            quantity: 2,
            selectedOptions: [
                $doubleGlass,
                $installation,
            ],
            taxRate: 21
        );

        $this->assertSame(500.0, $result['base_amount']);
        $this->assertSame(250.0, $result['supplements']);
        $this->assertSame(750.0, $result['unit_price']);
        $this->assertSame(1500.0, $result['subtotal']);
        $this->assertSame(315.0, $result['tax']);
        $this->assertSame(1815.0, $result['total']);
    }

    public function test_it_rejects_quantity_lower_than_or_equal_to_zero(): void
    {
        $product = Product::create([
            'name' => 'Mosquitera',
            'sku' => 'MOSQ',
            'base_price' => 100,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->calculate(
            product: $product,
            quantity: 0
        );
    }

    public function test_it_rejects_inactive_option_value(): void
    {
        $product = Product::create([
            'name' => 'Ventana PVC',
            'sku' => 'VENT-PVC',
            'base_price' => 500,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $option = ProductOption::create([
            'product_id' => $product->id,
            'name' => 'Cristal',
            'code' => 'glass',
            'required' => true,
            'active' => true,
        ]);

        $value = ProductOptionValue::create([
            'product_option_id' => $option->id,
            'name' => 'Doble',
            'code' => 'double',
            'price_adjustment_type' => 'fixed',
            'price_adjustment' => 100,
            'active' => false,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->calculate(
            product: $product,
            quantity: 1,
            selectedOptions: [$value]
        );
    }

    public function test_it_rejects_option_value_from_another_product(): void
    {
        $productA = Product::create([
            'name' => 'Ventana PVC',
            'sku' => 'VENT-PVC',
            'base_price' => 500,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $productB = Product::create([
            'name' => 'Ventana aluminio',
            'sku' => 'VENT-ALU',
            'base_price' => 600,
            'unit' => 'unidad',
            'active' => true,
        ]);

        $option = ProductOption::create([
            'product_id' => $productB->id,
            'name' => 'Cristal',
            'code' => 'glass',
            'required' => true,
            'active' => true,
        ]);

        $value = ProductOptionValue::create([
            'product_option_id' => $option->id,
            'name' => 'Doble',
            'code' => 'double',
            'price_adjustment_type' => 'fixed',
            'price_adjustment' => 100,
            'active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->calculate(
            product: $productA,
            quantity: 1,
            selectedOptions: [$value]
        );
    }
}