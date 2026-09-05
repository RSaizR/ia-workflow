<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('series')->default('FAC');
            $table->unsignedInteger('sequence');
            $table->string('invoice_number')->unique();

            $table->date('issued_at');

            /*
             * Datos del emisor.
             * Se guardan como snapshot.
             */
            $table->string('issuer_legal_name');
            $table->string('issuer_commercial_name')->nullable();
            $table->string('issuer_tax_id');
            $table->string('issuer_address');
            $table->string('issuer_postal_code')->nullable();
            $table->string('issuer_city')->nullable();
            $table->string('issuer_province')->nullable();
            $table->string('issuer_country')->nullable();
            $table->string('issuer_email')->nullable();
            $table->string('issuer_phone')->nullable();
            $table->string('issuer_website')->nullable();
            $table->string('issuer_iban')->nullable();

            /*
             * Datos del cliente.
             * También son snapshot.
             */
            $table->string('customer_name');
            $table->string('customer_company_name')->nullable();
            $table->string('customer_tax_id')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            /*
             * Importes.
             */
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(21);
            $table->decimal('tax', 12, 2);
            $table->decimal('total', 12, 2);

            $table->text('notes')->nullable();
            $table->text('footer')->nullable();

            $table->string('status')->default('issued');

            $table->timestamps();

            $table->unique([
                'series',
                'sequence',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};