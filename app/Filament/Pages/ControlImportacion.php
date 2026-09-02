<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use App\Services\Reportes\ControlImportacionService;
use App\Exports\ReporteTipoSolicitudExport;
use App\Exports\ResumenMensualExport;
use Maatwebsite\Excel\Facades\Excel;

class ControlImportacion extends Page implements HasForms
{
    protected static ?string $navigationLabel = 'Control de Importación';
    protected static string|UnitEnum|null $navigationGroup = NavigationGroupEnum::REPORTES->value;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Reporte de Control de Importación';
    protected string $view = 'filament.pages.control-importacion';
    public ?array $data = [];
    public bool $reporteGenerado = false;
    public int $totalCertificados = 0;
    public int $totalItems = 0;
    public array $resumenMensual = [];
    public array $reportes = [];

    public function mount(): void
    {
        $this->form->fill([
            'fecha_inicio' => now()->startOfYear()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]);
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filtros')
                    ->description(
                        'Seleccione el período de emisión de los certificados.'
                    )
                    ->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha emisión certificado inicio')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d'),
                        DatePicker::make('fecha_fin')
                            ->label('Fecha emisión certificado fin')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function generarReporte(
        ControlImportacionService $service
    ): void {
        $datos = $this->form->getState();
        if (empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
            Notification::make()
                ->title('Fechas requeridas')
                ->body('Debe seleccionar la fecha inicial y final.')
                ->danger()
                ->send();
            return;
        }
        if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
            Notification::make()
                ->title('Rango de fechas incorrecto')
                ->body('La fecha inicial no puede ser posterior a la fecha final.')
                ->danger()
                ->send();
            return;
        }
        $resultado = $service->generar(
            $datos['fecha_inicio'],
            $datos['fecha_fin']
        );
        $this->totalCertificados = $resultado['total_certificados'];
        $this->totalItems = $resultado['total_items'];
        $this->resumenMensual = $resultado['resumen_mensual'];
        $this->reportes = $resultado['reportes'];
        $this->reporteGenerado = true;
        Notification::make()
            ->title('Reporte generado')
            ->body(
                'La información fue generada correctamente.'
            )
            ->success()
            ->send();
    }

    public function exportarExcel(int $indice)
    {
        if (! isset($this->reportes[$indice])) {
            return;
        }
        $reporte = $this->reportes[$indice];
        $fechaInicio = $this->data['fecha_inicio'] ?? null;
        $fechaFin = $this->data['fecha_fin'] ?? null;
        return Excel::download(
            new ReporteTipoSolicitudExport(
                $reporte,
                $fechaInicio,
                $fechaFin,
            ),
            'reporte-' .
            str($reporte['tipo_solicitud'])->slug() .
            '.xlsx'
        );
    }

    public function exportarExcelResumen(string $tipo)
    {
        if (! in_array($tipo, ['certificados', 'items'], true)) {
            return;
        }
        if (empty($this->resumenMensual)) {
            Notification::make()
                ->title('No hay información para exportar')
                ->warning()
                ->send();
            return;
        }
        $fechaInicio = $this->data['fecha_inicio'] ?? null;
        $fechaFin = $this->data['fecha_fin'] ?? null;
        return Excel::download(
            new ResumenMensualExport(
                resumen: $this->resumenMensual,
                tipo: $tipo,
                fechaInicio: $fechaInicio,
                fechaFin: $fechaFin,
            ),
            $tipo === 'certificados'
                ? 'resumen-mensual-certificados.xlsx'
                : 'resumen-mensual-items.xlsx'
        );
    }

}
