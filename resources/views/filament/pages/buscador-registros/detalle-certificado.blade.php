<div class="text-xs">
    <x-filament::section>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <span class="font-medium text-gray-500">
                    Código Certificado
                </span>
                <div class="font-semibold">
                    {{ $certificado->codigo_certificado }}
                </div>
            </div>
            <div>
                <span class=" font-medium text-gray-500">
                    Fecha Solicitud
                </span>
                <div>
                    {{ $certificado->fecha_solicitud
                        ? \Carbon\Carbon::parse($certificado->fecha_solicitud)->format('d/m/Y')
                        : '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Fecha Emisión Certificado
                </span>
                <div>
                    {{ $certificado->fecha_emision_certificado
                        ? \Carbon\Carbon::parse($certificado->fecha_emision_certificado)->format('d/m/Y')
                        : '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Tipo de Solicitud
                </span>
                <div>
                    {{ $certificado->tipo_solicitud ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    NIT
                </span>
                <div>
                    {{ $certificado->nit ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Nombre de Empresa
                </span>
                <div>
                    {{ $certificado->nombre_empresa ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Uso
                </span>
                <div>
                    {{ $certificado->uso ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Tipo de Producto
                </span>
                <div>
                    {{ $certificado->tipo_producto ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Proveedor
                </span>
                <div>
                    {{ $certificado->proveedor ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Producto Refrigerado
                </span>
                <div>
                    {{ $certificado->producto_refrigerado ? 'Sí' : 'No' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Nro. Factura
                </span>
                <div>
                    {{ $certificado->nro_factura ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Monto Factura Bs.
                </span>
                <div>
                    {{ $certificado->monto_factura ?? '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Procedencia
                </span>
                <div>
                    {{ $certificado->procedencia ?: '-' }}
                </div>
            </div>
            <div>
                <span class="font-medium text-gray-500">
                    Usuario Origen
                </span>
                <div>
                    {{ $certificado->usuario_origen ?: '-' }}
                </div>
            </div>
        </div>
    </x-filament::section>
    <x-filament::section class="mt-4">
        <x-slot name="heading">
            Items del Certificado
        </x-slot>
        <div class="max-h-[50vh] overflow-auto rounded-lg border">
            <table class="w-full">
                <thead class="sticky top-0 z-10 bg-white dark:bg-gray-900">
                    <tr class="border-b">
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            N°
                        </th>
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            Cantidad
                        </th>
                        <th class="min-w-75 px-2 py-1.5 text-left font-semibold">
                            Producto
                        </th>
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            Registro Sanitario
                        </th>
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            Fecha Vencimiento
                        </th>
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            Nro. Lote
                        </th>
                        <th class="whitespace-nowrap px-2 py-1.5 text-left font-semibold">
                            Evaluación
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificado->items as $item)
                        <tr class="border-b">
                            <td class="whitespace-nowrap px-2 py-1.5">
                                {{ $item->numero_item ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1.5">
                                {{ $item->cantidad_medicamento ?? '-' }}
                            </td>
                            <td class="min-w-75 px-2 py-1.5">
                                {{ $item->producto ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1.5">
                                {{ $item->registro_sanitario ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1.5">
                                {{ $item->fecha_vencimiento
                                    ? \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y')
                                    : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1.5">
                                {{ $item->nro_lote ?? '-' }}
                            </td>
                            <td class="px-2 py-1.5">
                                {{ $item->evaluacion_item ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="px-3 py-5 text-center text-gray-500"
                            >
                                El certificado no tiene items registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</div>
