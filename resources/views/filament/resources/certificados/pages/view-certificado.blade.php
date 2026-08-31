<x-filament-panels::page>

    {{-- ==========================================================
        INFORMACIÓN DEL CERTIFICADO
    =========================================================== --}}

    <x-filament::section
        collapsible
        collapsed
        heading="Información del certificado"
        class="mb-6"
    >

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Código certificado
                </div>

                <div class="font-semibold">
                    {{ $record->codigo_certificado }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Tipo de solicitud
                </div>

                <div>
                    {{ $record->tipo_solicitud }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    NIT
                </div>

                <div>
                    {{ $record->nit }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-sm font-medium text-gray-500">
                    Nombre empresa
                </div>

                <div>
                    {{ $record->nombre_empresa }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Tipo de producto
                </div>

                <div>
                    {{ $record->tipo_producto }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Procedencia
                </div>

                <div>
                    {{ $record->procedencia }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Uso
                </div>

                <div>
                    {{ $record->uso }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Proveedor
                </div>

                <div>
                    {{ $record->proveedor }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Nro. factura
                </div>

                <div>
                    {{ $record->nro_factura }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Monto factura
                </div>

                <div>
                    {{ $record->monto_factura }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Fecha solicitud
                </div>

                <div>
                    {{ $record->fecha_solicitud?->format('d/m/Y') }}
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-500">
                    Fecha emisión
                </div>

                <div>
                    {{ $record->fecha_emision_certificado?->format('d/m/Y H:i') }}
                </div>
            </div>

        </div>

    </x-filament::section>


    {{-- ==========================================================
        ITEMS
    =========================================================== --}}

    <x-filament::section>

        <x-slot name="heading">
            Items del certificado
        </x-slot>

        {{ $this->table }}

    </x-filament::section>

</x-filament-panels::page>
