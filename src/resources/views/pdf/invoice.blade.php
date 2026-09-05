<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Factura {{ $invoice->invoice_number }}
    </title>

    <style>
        @page {
            margin: 35px 40px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        .header-table {
            width: 100%;
            margin-bottom: 35px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .company-name {
            margin: 0 0 5px;
            font-size: 20px;
            font-weight: bold;
        }

        .commercial-name {
            margin-bottom: 12px;
            color: #6b7280;
            font-size: 12px;
        }

        .invoice-title {
            margin: 0;
            text-align: right;
            font-size: 26px;
            font-weight: bold;
        }

        .invoice-number {
            margin-top: 6px;
            text-align: right;
            font-size: 13px;
        }

        .invoice-date {
            margin-top: 5px;
            text-align: right;
            color: #4b5563;
        }

        .party-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .party-table td {
            width: 50%;
            padding: 15px;
            vertical-align: top;
            border: 1px solid #d1d5db;
        }

        .party-title {
            margin-bottom: 10px;
            color: #6b7280;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .party-name {
            margin-bottom: 4px;
            font-size: 13px;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 9px 8px;
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .description {
            font-weight: bold;
        }

        .option {
            margin-top: 3px;
            color: #6b7280;
            font-size: 9px;
        }

        .totals-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 42%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 8px;
        }

        .totals-table .label {
            color: #4b5563;
        }

        .totals-table .total-row td {
            padding-top: 10px;
            border-top: 2px solid #111827;
            font-size: 14px;
            font-weight: bold;
        }

        .payment-box {
            margin-top: 20px;
            padding: 12px 15px;
            border: 1px solid #d1d5db;
        }

        .payment-title {
            margin-bottom: 6px;
            font-weight: bold;
        }

        .notes {
            margin-top: 25px;
        }

        .notes-title {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            right: 40px;
            bottom: 20px;
            left: 40px;

            padding-top: 8px;
            border-top: 1px solid #d1d5db;

            color: #6b7280;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>

<table class="header-table">
    <tr>
        <td width="60%">
            <div class="company-name">
                {{ $invoice->issuer_legal_name }}
            </div>

            @if ($invoice->issuer_commercial_name)
                <div class="commercial-name">
                    {{ $invoice->issuer_commercial_name }}
                </div>
            @endif

            <div>
                NIF / CIF:
                <strong>{{ $invoice->issuer_tax_id }}</strong>
            </div>

            <div>
                {{ $invoice->issuer_address }}
            </div>

            <div>
                {{ $invoice->issuer_postal_code }}
                {{ $invoice->issuer_city }}

                @if ($invoice->issuer_province)
                    ({{ $invoice->issuer_province }})
                @endif
            </div>

            @if ($invoice->issuer_country)
                <div>
                    {{ $invoice->issuer_country }}
                </div>
            @endif

            @if ($invoice->issuer_email)
                <div>
                    {{ $invoice->issuer_email }}
                </div>
            @endif

            @if ($invoice->issuer_phone)
                <div>
                    {{ $invoice->issuer_phone }}
                </div>
            @endif
        </td>

        <td width="40%">
            <h1 class="invoice-title">
                FACTURA
            </h1>

            <div class="invoice-number">
                {{ $invoice->invoice_number }}
            </div>

            <div class="invoice-date">
                Fecha de emisión:
                {{ $invoice->issued_at->format('d/m/Y') }}
            </div>

            <div class="invoice-date">
                Presupuesto:
                #{{ $invoice->quote_id }}
            </div>
        </td>
    </tr>
</table>

<table class="party-table">
    <tr>
        <td>
            <div class="party-title">
                Emisor
            </div>

            <div class="party-name">
                {{ $invoice->issuer_legal_name }}
            </div>

            <div>
                NIF / CIF: {{ $invoice->issuer_tax_id }}
            </div>

            <div>
                {{ $invoice->issuer_address }}
            </div>

            <div>
                {{ $invoice->issuer_postal_code }}
                {{ $invoice->issuer_city }}
            </div>
        </td>

        <td>
            <div class="party-title">
                Cliente
            </div>

            <div class="party-name">
                {{ $invoice->customer_company_name ?: $invoice->customer_name }}
            </div>

            @if (
                $invoice->customer_company_name
                && $invoice->customer_name
            )
                <div>
                    {{ $invoice->customer_name }}
                </div>
            @endif

            <div>
                NIF / CIF:
                {{ $invoice->customer_tax_id }}
            </div>

            <div>
                {{ $invoice->customer_address }}
            </div>

            @if ($invoice->customer_email)
                <div>
                    {{ $invoice->customer_email }}
                </div>
            @endif

            @if ($invoice->customer_phone)
                <div>
                    {{ $invoice->customer_phone }}
                </div>
            @endif
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th>
                Concepto
            </th>

            <th
                class="text-center"
                width="12%"
            >
                Cantidad
            </th>

            <th
                class="text-right"
                width="18%"
            >
                Precio unitario
            </th>

            <th
                class="text-right"
                width="18%"
            >
                Importe
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td>
                    <div class="description">
                        {{ $item->description }}
                    </div>

                    @if (! empty($item->metadata['options']))
                        @foreach ($item->metadata['options'] as $option)
                            <div class="option">
                                {{ $option['option_name'] ?? $option['name'] ?? '' }}
                                @if (
                                    isset($option['value_name'])
                                    || isset($option['value'])
                                )
                                    :
                                    {{ $option['value_name'] ?? $option['value'] }}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </td>

                <td class="text-center">
                    {{ number_format(
                        $item->quantity,
                        2,
                        ',',
                        '.'
                    ) }}
                </td>

                <td class="text-right">
                    {{ number_format(
                        $item->unit_price,
                        2,
                        ',',
                        '.'
                    ) }}
                    €
                </td>

                <td class="text-right">
                    {{ number_format(
                        $item->subtotal,
                        2,
                        ',',
                        '.'
                    ) }}
                    €
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="totals-wrapper">
    <table class="totals-table">
        <tr>
            <td class="label">
                Base imponible
            </td>

            <td class="text-right">
                {{ number_format(
                    $invoice->subtotal,
                    2,
                    ',',
                    '.'
                ) }}
                €
            </td>
        </tr>

        <tr>
            <td class="label">
                IVA
                {{ number_format(
                    $invoice->tax_rate,
                    2,
                    ',',
                    '.'
                ) }}
                %
            </td>

            <td class="text-right">
                {{ number_format(
                    $invoice->tax,
                    2,
                    ',',
                    '.'
                ) }}
                €
            </td>
        </tr>

        <tr class="total-row">
            <td>
                TOTAL
            </td>

            <td class="text-right">
                {{ number_format(
                    $invoice->total,
                    2,
                    ',',
                    '.'
                ) }}
                €
            </td>
        </tr>
    </table>
</div>

@if ($invoice->issuer_iban)
    <div class="payment-box">
        <div class="payment-title">
            Datos de pago
        </div>

        <div>
            IBAN:
            {{ $invoice->issuer_iban }}
        </div>

        <div>
            Concepto:
            {{ $invoice->invoice_number }}
        </div>
    </div>
@endif

@if ($invoice->notes)
    <div class="notes">
        <div class="notes-title">
            Observaciones
        </div>

        <div>
            {{ $invoice->notes }}
        </div>
    </div>
@endif

@if ($invoice->footer)
    <div class="footer">
        {{ $invoice->footer }}
    </div>
@endif

</body>
</html>