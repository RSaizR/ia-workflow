<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice): DomPdf
    {
        $invoice->load('items');

        return Pdf::loadView(
            'pdf.invoice',
            [
                'invoice' => $invoice,
            ]
        )
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans');
    }

    public function filename(Invoice $invoice): string
    {
        return sprintf(
            'factura-%s.pdf',
            str_replace(
                ['/', '\\', ' '],
                '-',
                $invoice->invoice_number
            )
        );
    }
}