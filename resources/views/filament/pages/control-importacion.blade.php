<x-filament-panels::page>
    <form wire:submit="generarReporte">
        {{ $this->form }}
        <div class="mt-5 flex justify-center">
            <x-filament::button
                type="submit"
                icon="heroicon-o-chart-bar"
            >
                Generar reporte
            </x-filament::button>
        </div>
    </form>

    @if ($reporteGenerado)
        <x-filament::section class="mt-8">
            <x-slot name="heading">
                Resumen del período
            </x-slot>
            <x-slot name="description">
                Información consolidada del período seleccionado.
            </x-slot>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-primary-50 p-3 dark:bg-primary-500/10">
                            <x-filament::icon
                                icon="heroicon-o-document-text"
                                class="h-6 w-6 text-primary-600 dark:text-primary-400"
                            />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total certificados
                            </p>
                            <p class="text-2xl font-bold">
                                {{ number_format($totalCertificados) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-primary-50 p-3 dark:bg-primary-500/10">
                            <x-filament::icon
                                icon="heroicon-o-list-bullet"
                                class="h-6 w-6 text-primary-600 dark:text-primary-400"
                            />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total ítems
                            </p>
                            <p class="text-2xl font-bold">
                                {{ number_format($totalItems) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        @php
            $reporteId = 'reporte-certificados';
        @endphp
        <x-filament::section class="mt-8">
            <x-slot name="heading">
                Total de Certificados del período
            </x-slot>
            <x-slot name="description">
                Cantidad de certificados por mes y tipo de solicitudes.
            </x-slot>
            <div class="flex flex-wrap gap-2 mb-6">
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-table-cells"
                    x-on:click="$dispatch('toggle-tabla-{{ $reporteId }}')"
                >
                    Tabla
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-chart-bar"
                    x-on:click="$dispatch('toggle-grafico-{{ $reporteId }}')"
                >
                    Gráfico
                </x-filament::button>
                <x-filament::button
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                    wire:click="exportarExcelResumen('certificados')"
                >
                    Excel
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-printer"
                    x-on:click="window.print()"
                >
                    Imprimir
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-photo"
                    x-on:click="
                        const canvas = document.getElementById(
                            'grafico-{{ $reporteId }}'
                        );
                        const enlace = document.createElement('a');
                        enlace.download =
                            'grafico-{{ $reporteId }}.png';
                        enlace.href = canvas.toDataURL('image/png');
                        enlace.click();
                    "
                >
                    PNG
                </x-filament::button>
            </div>
            <div
                    x-data="{ visible: true }"
                    x-on:toggle-tabla-{{ $reporteId }}.window="visible = !visible"
                    x-show="visible"
                    x-transition
                >
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b
                                        bg-gray-50
                                        dark:border-gray-700
                                        dark:bg-gray-800"
                            >
                                <th
                                    class="whitespace-nowrap
                                            px-4 py-3 text-left
                                            font-semibold"
                                >
                                    MES
                                </th>
                                @foreach (
                                    $resumenMensual['tipos_solicitud']
                                    as $tipoSolicitud
                                )
                                    <th
                                        class="whitespace-nowrap
                                                px-4 py-3 text-center
                                                font-semibold"
                                    >
                                        {{ $tipoSolicitud }}
                                    </th>
                                @endforeach
                                <th
                                    class="whitespace-nowrap
                                            px-4 py-3 text-right
                                            font-semibold"
                                >
                                    TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (
                                $resumenMensual['certificados']
                                as $fila
                            )
                                <tr
                                    class="border-b
                                            hover:bg-gray-50
                                            dark:border-gray-700
                                            dark:hover:bg-gray-800"
                                >
                                    <td
                                        class="whitespace-nowrap
                                                px-4 py-3 font-medium"
                                    >
                                        {{ $fila['mes'] }}
                                    </td>
                                    @foreach (
                                        $resumenMensual['tipos_solicitud']
                                        as $tipoSolicitud
                                    )
                                        <td
                                            class="px-4 py-3
                                                    text-center"
                                        >
                                            {{
                                                number_format(
                                                    $fila['totales'][$tipoSolicitud]
                                                    ?? 0
                                                )
                                            }}
                                        </td>
                                    @endforeach
                                    <td
                                        class="px-4 py-3 text-right font-semibold"
                                    >
                                        {{ number_format($fila['total'] ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-gray-100
                                        font-bold
                                        dark:bg-gray-800"
                            >
                                <td
                                    class="px-4 py-3"
                                >
                                    TOTAL
                                </td>
                                @foreach (
                                    $resumenMensual['tipos_solicitud']
                                    as $tipoSolicitud
                                )
                                    <td
                                        class="px-4 py-3
                                                text-center"
                                    >
                                        {{
                                            number_format(
                                                $resumenMensual[
                                                    'totales_certificados'
                                                ][$tipoSolicitud]
                                                ?? 0
                                            )
                                        }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right">
                                    {{
                                        number_format(
                                            array_sum($resumenMensual['totales_certificados'] ?? [])
                                        )
                                    }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div
                class="mt-8"
                x-data="{ visible: true }"
                x-on:toggle-grafico-{{ $reporteId }}.window="visible = !visible"
                x-show="visible"
                x-transition
            >
                <div class="mb-3">
                    <h3
                        class="text-sm font-semibold
                            text-gray-700
                            dark:text-gray-200"
                    >
                        Distribución mensual de certificados por tipo de solicitud
                    </h3>
                </div>
                <div
                    class="h-80 rounded-xl border
                        border-gray-200
                        bg-white p-4
                        dark:border-gray-700
                        dark:bg-gray-900"
                    x-data="{
                        categorias: @js(
                            $resumenMensual['grafico_certificados']['categorias'] ?? []
                        ),
                        series: @js(
                            $resumenMensual['grafico_certificados']['series'] ?? []
                        ),
                        chart: null,
                        init() {
                            this.$nextTick(() => {
                                this.crearGrafico();
                            });
                        },
                        crearGrafico() {
                            const canvas = this.$refs.canvas;
                            if (!canvas || typeof Chart === 'undefined') {
                                return;
                            }
                            if (this.chart) {
                                this.chart.destroy();
                                this.chart = null;
                            }
                            const datasets = this.series.map((serie) => ({
                                label: serie.nombre,
                                data: serie.datos,
                                borderWidth: 1,
                            }));
                            this.chart = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: this.categorias,
                                    datasets: datasets,
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        intersect: false,
                                        mode: 'index',
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0,
                                            },
                                        },
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        },
                                    },
                                },
                            });
                        },
                    }"
                >
                    <canvas
                        x-ref="canvas"
                        id="grafico-{{ $reporteId }}"
                    ></canvas>
                </div>
            </div>
        </x-filament::section>

        @php
            $reporteId = 'reporte-items';
        @endphp
        <x-filament::section class="mt-8">
            <x-slot name="heading">
                Total de Items del período
            </x-slot>
            <x-slot name="description">
                Cantidad de ítems por mes y tipo de solicitudes.
            </x-slot>
            <div class="flex flex-wrap gap-2 mb-6">
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-table-cells"
                    x-on:click="$dispatch('toggle-tabla-{{ $reporteId }}')"
                >
                    Tabla
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-chart-bar"
                    x-on:click="$dispatch('toggle-grafico-{{ $reporteId }}')"
                >
                    Gráfico
                </x-filament::button>
                <x-filament::button
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                    wire:click="exportarExcelResumen('items')"
                >
                    Excel
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-printer"
                    x-on:click="window.print()"
                >
                    Imprimir
                </x-filament::button>
                <x-filament::button
                    color="gray"
                    icon="heroicon-o-photo"
                    x-on:click="
                        const canvas = document.getElementById(
                            'grafico-{{ $reporteId }}'
                        );
                        const enlace = document.createElement('a');
                        enlace.download =
                            'grafico-{{ $reporteId }}.png';
                        enlace.href = canvas.toDataURL('image/png');
                        enlace.click();
                    "
                >
                    PNG
                </x-filament::button>
            </div>
            <div
                    x-data="{ visible: true }"
                    x-on:toggle-tabla-{{ $reporteId }}.window="visible = !visible"
                    x-show="visible"
                    x-transition
                >
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b
                                        bg-gray-50
                                        dark:border-gray-700
                                        dark:bg-gray-800"
                            >
                                <th
                                    class="whitespace-nowrap
                                            px-4 py-3 text-left
                                            font-semibold"
                                >
                                    MES
                                </th>
                                @foreach (
                                    $resumenMensual['tipos_solicitud']
                                    as $tipoSolicitud
                                )
                                    <th
                                        class="whitespace-nowrap
                                                px-4 py-3 text-center
                                                font-semibold"
                                    >
                                        {{ $tipoSolicitud }}
                                    </th>
                                @endforeach
                                <th
                                    class="whitespace-nowrap
                                            px-4 py-3 text-right
                                            font-semibold"
                                >
                                    TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (
                                $resumenMensual['items']
                                as $fila
                            )
                                <tr
                                    class="border-b
                                            hover:bg-gray-50
                                            dark:border-gray-700
                                            dark:hover:bg-gray-800"
                                >
                                    <td
                                        class="whitespace-nowrap
                                                px-4 py-3 font-medium"
                                    >
                                        {{ $fila['mes'] }}
                                    </td>
                                    @foreach (
                                        $resumenMensual['tipos_solicitud']
                                        as $tipoSolicitud
                                    )
                                        <td
                                            class="px-4 py-3
                                                    text-center"
                                        >
                                            {{
                                                number_format(
                                                    $fila['totales'][$tipoSolicitud]
                                                    ?? 0
                                                )
                                            }}
                                        </td>
                                    @endforeach
                                    <td
                                        class="px-4 py-3 text-right font-semibold"
                                    >
                                        {{ number_format($fila['total'] ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-gray-100
                                        font-bold
                                        dark:bg-gray-800"
                            >
                                <td
                                    class="px-4 py-3"
                                >
                                    TOTAL
                                </td>
                                @foreach (
                                    $resumenMensual['tipos_solicitud']
                                    as $tipoSolicitud
                                )
                                    <td
                                        class="px-4 py-3
                                                text-center"
                                    >
                                        {{
                                            number_format(
                                                $resumenMensual[
                                                    'totales_items'
                                                ][$tipoSolicitud]
                                                ?? 0
                                            )
                                        }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right">
                                    {{
                                        number_format(
                                            array_sum($resumenMensual['totales_items'] ?? [])
                                        )
                                    }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div
                class="mt-8"
                x-data="{ visible: true }"
                x-on:toggle-grafico-{{ $reporteId }}.window="visible = !visible"
                x-show="visible"
                x-transition
            >
                <div class="mb-3">
                    <h3
                        class="text-sm font-semibold
                            text-gray-700
                            dark:text-gray-200"
                    >
                        Distribución mensual de ítems por tipo de solicitud
                    </h3>
                </div>
                <div
                    class="h-80 rounded-xl border
                        border-gray-200
                        bg-white p-4
                        dark:border-gray-700
                        dark:bg-gray-900"
                    x-data="{
                        categorias: @js(
                            $resumenMensual['grafico_items']['categorias'] ?? []
                        ),
                        series: @js(
                            $resumenMensual['grafico_items']['series'] ?? []
                        ),
                        chart: null,
                        init() {
                            this.$nextTick(() => {
                                this.crearGrafico();
                            });
                        },
                        crearGrafico() {
                            const canvas = this.$refs.canvas;
                            if (!canvas || typeof Chart === 'undefined') {
                                return;
                            }
                            if (this.chart) {
                                this.chart.destroy();
                                this.chart = null;
                            }
                            const datasets = this.series.map((serie) => ({
                                label: serie.nombre,
                                data: serie.datos,
                                borderWidth: 1,
                            }));
                            this.chart = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: this.categorias,
                                    datasets: datasets,
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        intersect: false,
                                        mode: 'index',
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0,
                                            },
                                        },
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        },
                                    },
                                },
                            });
                        },
                    }"
                >
                    <canvas
                        x-ref="canvas"
                        id="grafico-{{ $reporteId }}"
                    ></canvas>
                </div>
            </div>
        </x-filament::section>

        @foreach ($reportes as $indice => $reporte)
            @php
                $reporteId = 'reporte-' . $indice;
            @endphp
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    {{ $reporte['titulo'] }}
                </x-slot>
                <x-slot name="description">
                    Cantidad mensual de ítems por tipo de producto.
                </x-slot>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Total de certificados
                        </div>
                        <div class="mt-1 text-2xl font-bold">
                            {{ number_format($reporte['total_certificados']) }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Total de ítems
                        </div>
                        <div class="mt-1 text-2xl font-bold">
                            {{ number_format($reporte['total_items']) }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-table-cells"
                        x-on:click="$dispatch('toggle-tabla-{{ $reporteId }}')"
                    >
                        Tabla
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-chart-bar"
                        x-on:click="$dispatch('toggle-grafico-{{ $reporteId }}')"
                    >
                        Gráfico
                    </x-filament::button>
                    <x-filament::button
                        color="success"
                        icon="heroicon-o-arrow-down-tray"
                        wire:click="exportarExcel({{ $indice }})"
                    >
                        Excel
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-printer"
                        x-on:click="window.print()"
                    >
                        Imprimir
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-photo"
                        x-on:click="
                            const canvas = document.getElementById(
                                'grafico-{{ $reporteId }}'
                            );
                            const enlace = document.createElement('a');
                            enlace.download =
                                'grafico-{{ $reporteId }}.png';
                            enlace.href = canvas.toDataURL('image/png');
                            enlace.click();
                        "
                    >
                        PNG
                    </x-filament::button>
                </div>
                <div
                    x-data="{ visible: true }"
                    x-on:toggle-tabla-{{ $reporteId }}.window="visible = !visible"
                    x-show="visible"
                    x-transition
                >
                    <div class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                        Cantidad de ítems por mes y tipo de producto.
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-4 py-3 text-left font-semibold">
                                    Mes
                                </th>
                                @foreach ($reporte['tipos_producto'] as $tipoProducto)
                                    <th class="px-4 py-3 text-right font-semibold">
                                        {{ $tipoProducto }}
                                    </th>
                                @endforeach
                                <th class="px-4 py-3 text-right font-semibold">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reporte['filas'] as $fila)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-medium">
                                        {{ ucfirst($fila['mes']) }}
                                        {{ $fila['anio'] }}
                                    </td>
                                    @foreach ($reporte['tipos_producto'] as $tipoProducto)
                                        <td class="px-4 py-3 text-right">
                                            {{ number_format(
                                                $fila['productos'][$tipoProducto] ?? 0
                                            ) }}
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-right font-semibold">
                                        {{ number_format($fila['total']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-bold">
                                <td class="px-4 py-3">
                                    TOTAL
                                </td>
                                @foreach ($reporte['tipos_producto'] as $tipoProducto)
                                    <td class="px-4 py-3 text-right">
                                        {{ number_format(
                                            $reporte['totales'][$tipoProducto] ?? 0
                                        ) }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right">
                                    {{ number_format($reporte['total']) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    El número mensual corresponde al conteo de ítems registrados.
                </p>
                <div
                    class="mt-8"
                    x-data="{ visible: true }"
                    x-on:toggle-grafico-{{ $reporteId }}.window="visible = !visible"
                    x-show="visible"
                    x-transition
                >
                    <div class="mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Distribución mensual
                        </h3>
                    </div>
                    <div
                        class="h-80 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900"
                        x-data="{
                            categorias: @js($reporte['grafico']['categorias']),
                            series: @js($reporte['grafico']['series']),
                            chart: null,
                            init() {
                                this.$nextTick(() => {
                                    this.crearGrafico();
                                });
                            },
                            crearGrafico() {
                                const canvas = this.$refs.canvas;
                                if (!canvas || typeof Chart === 'undefined') {
                                    return;
                                }
                                if (this.chart) {
                                    this.chart.destroy();
                                    this.chart = null;
                                }
                                const datasets = this.series.map((serie) => ({
                                    label: serie.nombre,
                                    data: serie.datos,
                                    borderWidth: 1,
                                }));
                                this.chart = new Chart(canvas, {
                                    type: 'bar',
                                    data: {
                                        labels: this.categorias,
                                        datasets: datasets,
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        interaction: {
                                            intersect: false,
                                            mode: 'index',
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    precision: 0,
                                                },
                                            },
                                        },
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                            },
                                        },
                                    },
                                });
                            },
                        }"
                    >
                        <canvas
                            x-ref="canvas"
                            id="grafico-{{ $reporteId }}"
                        ></canvas>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    @endif

</x-filament-panels::page>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
