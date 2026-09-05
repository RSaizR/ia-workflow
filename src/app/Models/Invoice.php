<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'quote_id',

        'series',
        'sequence',
        'invoice_number',
        'issued_at',

        'issuer_legal_name',
        'issuer_commercial_name',
        'issuer_tax_id',
        'issuer_address',
        'issuer_postal_code',
        'issuer_city',
        'issuer_province',
        'issuer_country',
        'issuer_email',
        'issuer_phone',
        'issuer_website',
        'issuer_iban',

        'customer_name',
        'customer_company_name',
        'customer_tax_id',
        'customer_address',
        'customer_email',
        'customer_phone',

        'subtotal',
        'tax_rate',
        'tax',
        'total',

        'notes',
        'footer',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',

            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}