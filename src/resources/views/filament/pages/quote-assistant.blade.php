<x-filament-panels::page>

    <style>
        .quote-assistant {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .quote-form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .quote-result-actions {
            display: flex;
            justify-content: flex-end;
        }

        .quote-result-grid {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        .quote-items {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .quote-info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .quote-label {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .quote-value {
            font-weight: 500;
        }

        .quote-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .quote-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
        }

        .quote-card-value {
            margin-top: 0.25rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .quote-options-title {
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .quote-options-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .quote-economic-summary {
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
        }

        .quote-economic-title {
            margin-bottom: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .quote-economic-rows {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .quote-economic-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .quote-economic-label {
            color: #4b5563;
        }

        .quote-total {
            margin-top: 0.25rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .quote-total-label {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .quote-total-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .quote-message-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .quote-message {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }

        .quote-status {
            padding: 1rem 1.25rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-weight: 500;
        }

        .quote-loading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .quote-loading-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-top-color: #6b7280;
            border-radius: 50%;
            animation: quote-spin 0.75s linear infinite;
        }

        @keyframes quote-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1024px) {
            .quote-result-grid {
                grid-template-columns: 1fr;
            }

            .quote-options-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .quote-summary-grid,
            .quote-options-grid {
                grid-template-columns: 1fr;
            }
        }

        .dark .quote-label,
        .dark .quote-loading {
            color: #9ca3af;
        }

        .dark .quote-card,
        .dark .quote-economic-summary,
        .dark .quote-message,
        .dark .quote-status {
            border-color: #374151;
        }

        .dark .quote-economic-label {
            color: #d1d5db;
        }

        .dark .quote-total {
            border-color: #374151;
        }
    </style>

    <div class="quote-assistant">

        <x-filament::section>
            <x-slot name="heading">
                Nueva solicitud
            </x-slot>

            <x-slot name="description">
                Introduce el mensaje o solicitud recibida del cliente para generar una propuesta de presupuesto.
            </x-slot>

            <form wire:submit="interpretRequest">
                {{ $this->form }}

                <div
                    wire:loading
                    wire:target="interpretRequest"
                    class="quote-loading"
                >
                    <span class="quote-loading-spinner"></span>

                    <span>
                        La IA está analizando la solicitud y comparándola con el catálogo...
                    </span>
                </div>

                <div class="quote-form-actions">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-sparkles"
                        size="lg"
                        wire:loading.attr="disabled"
                        wire:target="interpretRequest"
                    >
                        <span
                            wire:loading.remove
                            wire:target="interpretRequest"
                        >
                            Interpretar solicitud
                        </span>

                        <span
                            wire:loading
                            wire:target="interpretRequest"
                        >
                            Interpretando...
                        </span>
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        @if ($result)

            @if (! empty($result['errors']))
                <x-filament::section>
                    <x-slot name="heading">
                        Errores detectados
                    </x-slot>

                    <div class="quote-message-list">
                        @foreach ($result['errors'] as $error)
                            <div class="quote-message">
                                {{ $error['message'] }}
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            @if (! empty($result['missing_information']))
                <x-filament::section>
                    <x-slot name="heading">
                        Información pendiente
                    </x-slot>

                    <div class="quote-message-list">
                        @foreach ($result['missing_information'] as $missing)
                            <div class="quote-message">
                                {{ is_array($missing) ? $missing['message'] : $missing }}
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            <div class="quote-status">
                @if ($result['complete'])
                    Solicitud completa y lista para revisar.
                @else
                    Presupuesto parcial. Hay información pendiente antes de poder confirmarlo.
                @endif
            </div>

            <div class="quote-result-grid">

                @if (! empty($result['customer']))
                    <x-filament::section>
                        <x-slot name="heading">
                            Cliente detectado
                        </x-slot>

                        <div class="quote-info-list">

                            <div>
                                <span class="quote-label">
                                    Nombre
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['name'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="quote-label">
                                    Empresa
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['companyName'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="quote-label">
                                    Correo electrónico
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['email'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="quote-label">
                                    Teléfono
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['phone'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="quote-label">
                                    NIF / CIF
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['taxId'] ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="quote-label">
                                    Dirección
                                </span>

                                <div class="quote-value">
                                    {{ $result['customer']['address'] ?? '-' }}
                                </div>
                            </div>

                        </div>
                    </x-filament::section>
                @endif

                <div class="quote-items">

                    @foreach ($result['items'] as $item)

                        <x-filament::section>

                            <x-slot name="heading">
                                {{ $item['product_name'] }}
                            </x-slot>

                            <x-slot name="description">
                                {{ $item['product_code'] }}
                            </x-slot>

                            <div class="quote-items">

                                <div class="quote-summary-grid">

                                    <div class="quote-card">
                                        <span class="quote-label">
                                            Cantidad
                                        </span>

                                        <div class="quote-card-value">
                                            {{ $item['quantity'] }}
                                        </div>
                                    </div>

                                    <div class="quote-card">
                                        <span class="quote-label">
                                            Precio unitario
                                        </span>

                                        <div class="quote-card-value">
                                            {{ number_format($item['pricing']['unit_price'], 2, ',', '.') }} €
                                        </div>
                                    </div>

                                </div>

                                @if (! empty($item['options']))
                                    <div>

                                        <div class="quote-options-title">
                                            Configuración
                                        </div>

                                        <div class="quote-options-grid">

                                            @foreach ($item['options'] as $option)

                                                <div class="quote-card">

                                                    <span class="quote-label">
                                                        {{ $option['option_name'] }}
                                                    </span>

                                                    <div class="quote-value">
                                                        {{ $option['value_name'] }}
                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>
                                    </div>
                                @endif

                                <div class="quote-economic-summary">

                                    <div class="quote-economic-title">
                                        Resumen económico
                                    </div>

                                    <div class="quote-economic-rows">

                                        <div class="quote-economic-row">
                                            <span class="quote-economic-label">
                                                Precio base
                                            </span>

                                            <span>
                                                {{ number_format($item['pricing']['base_amount'], 2, ',', '.') }} €
                                            </span>
                                        </div>

                                        <div class="quote-economic-row">
                                            <span class="quote-economic-label">
                                                Suplementos
                                            </span>

                                            <span>
                                                {{ number_format($item['pricing']['supplements'], 2, ',', '.') }} €
                                            </span>
                                        </div>

                                        <div class="quote-economic-row">
                                            <span class="quote-economic-label">
                                                Subtotal
                                            </span>

                                            <span>
                                                {{ number_format($item['pricing']['subtotal'], 2, ',', '.') }} €
                                            </span>
                                        </div>

                                        <div class="quote-economic-row">
                                            <span class="quote-economic-label">
                                                IVA ({{ number_format($item['pricing']['tax_rate'], 0) }}%)
                                            </span>

                                            <span>
                                                {{ number_format($item['pricing']['tax'], 2, ',', '.') }} €
                                            </span>
                                        </div>

                                        <div class="quote-total">
                                            <div class="quote-economic-row">

                                                <span class="quote-total-label">
                                                    Total
                                                </span>

                                                <span class="quote-total-value">
                                                    {{ number_format($item['pricing']['total'], 2, ',', '.') }} €
                                                </span>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </x-filament::section>

                    @endforeach

                </div>

            </div>

            @if ($result['valid'] && $result['complete'] && ! empty($result['items']))

                <div class="quote-result-actions">

                    <x-filament::button
                        wire:click="saveQuote"
                        wire:loading.attr="disabled"
                        wire:target="saveQuote"
                        icon="heroicon-o-document-check"
                        size="lg"
                    >
                        <span
                            wire:loading.remove
                            wire:target="saveQuote"
                        >
                            Guardar presupuesto
                        </span>

                        <span
                            wire:loading
                            wire:target="saveQuote"
                        >
                            Guardando...
                        </span>
                    </x-filament::button>

                </div>

            @endif

        @endif

    </div>

</x-filament-panels::page>