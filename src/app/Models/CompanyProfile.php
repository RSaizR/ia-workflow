<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'legal_name',
        'commercial_name',
        'tax_id',
        'address',
        'postal_code',
        'city',
        'province',
        'country',
        'email',
        'phone',
        'website',
        'iban',
        'invoice_series',
        'logo_path',
        'invoice_footer',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}