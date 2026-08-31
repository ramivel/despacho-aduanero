<?php

namespace App\Filament\Resources\Importacions\Pages;

use App\Filament\Resources\Importacions\ImportacionResource;
use App\Models\ImportacionRegistro;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use App\Services\Importaciones\ImportacionService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\ToggleColumn;

use Filament\Actions\Action;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;


class ImportarArchivo extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ImportacionResource::class;
    protected string $view = 'filament.resources.importacions.pages.importar-archivo';
    public ?array $data = [];
    public ?int $importacionId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Archivo de importación')
                    ->description(
                        'Seleccione el archivo generado por el sistema.'
                    )
                    ->schema([
                        FileUpload::make('archivo')
                            ->label('Archivo')
                            ->required()
                            ->validationMessages([
                                'required' => 'Debe subir el archivo de importación, es obligatorio.',
                            ])
                            ->disk('local')
                            ->directory('importaciones')
                            ->visibility('private')
                            ->preserveFilenames()
                            /*->acceptedFileTypes([
                                'text/html',
                                'text/plain',
                                'text/csv',
                            ])*/
                            ->maxSize(102400)
                            ->helperText(
                                'Tamaño máximo: 50 MB.'
                            ),
                    ]),

            ])
            ->statePath('data');
    }

    public function validar(
        ImportacionService $service
    ): void {
        $datos = $this->form->getState();
        $archivo = $datos['archivo'] ?? null;

        if (!$archivo) {
            Notification::make()
                ->title('Archivo requerido')
                ->body('Debe seleccionar un archivo.')
                ->danger()
                ->send();
            return;
        }
        try {
            $disk = Storage::disk('local');
            $path = $disk->path($archivo);
            $importacion = $service->procesar(
                $path,
                basename($path),
                Auth::id()
            );
            Notification::make()
                ->title('Archivo validado')
                ->body(
                    "Se procesaron {$importacion->total_items} registros."
                )
                ->success()
                ->send();
            $this->importacionId = $importacion->id;
            $this->resetTable();
            $this->resetPage();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Error al validar')
                ->body(
                    'No fue posible procesar el archivo.'
                )
                ->danger()
                ->send();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ImportacionRegistro::query()
                    ->when(
                        $this->importacionId !== null,
                        fn ($query) => $query->where(
                            'importacion_id',
                            $this->importacionId
                        ),
                        fn ($query) => $query->whereRaw('1 = 0')
                    )
            )
            ->defaultPaginationPageOption(500)
            ->paginationPageOptions([
                500,
                1000,
            ])
            ->defaultSort(
                'numero_fila',
                'asc'
            )
            ->columns([
                ToggleColumn::make('seleccionado')
                    ->label('Importar')
                    ->disabled(
                        fn (ImportacionRegistro $record): bool =>
                            $record->estado !== 'VALIDO'
                    )
                    ->onColor('success')
                    ->offColor('gray')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark'),
                TextColumn::make('numero_fila')
                    ->label('Fila')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VALIDO' => 'success',
                        'EXISTENTE' => 'warning',
                        'ERROR' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('codigo_certificado')
                    ->label('Certificado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make(
                    'datos_certificado.nombre_empresa'
                )
                    ->label('Empresa')
                    ->limit(35),
                TextColumn::make(
                    'datos_item.producto'
                )
                    ->label('Producto')
                    ->limit(45),
                TextColumn::make(
                    'datos_item.nro_lote'
                )
                    ->label('Lote'),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'VALIDO' => 'Válidos',
                        'EXISTENTE' => 'Ya registrados',
                        'ERROR' => 'Con errores',
                    ]),
            ])
            ->headerActions([
                Action::make('seleccionar_validos')
                    ->label('Seleccionar válidos')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function () {
                        ImportacionRegistro::query()
                            ->where(
                                'importacion_id',
                                $this->importacionId
                            )
                            ->where(
                                'estado',
                                'VALIDO'
                            )
                            ->update([
                                'seleccionado' => true,
                            ]);
                    }),
                Action::make('deseleccionar')
                    ->label('Deseleccionar')
                    ->icon('heroicon-o-x-mark')
                    ->action(function () {
                        ImportacionRegistro::query()
                            ->where(
                                'importacion_id',
                                $this->importacionId
                            )
                            ->where(
                                'estado',
                                'VALIDO'
                            )
                            ->update([
                                'seleccionado' => false,
                            ]);
                    }),
            ])
            ->striped()
            ->paginated(true);
    }

    public function resumenValidacion(): array
    {
        if (! $this->importacionId) {
            return [
                'total' => 0,
                'validos' => 0,
                'existentes' => 0,
                'errores' => 0,
            ];
        }

        $resultado = ImportacionRegistro::query()
            ->where('importacion_id', $this->importacionId)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(*) FILTER (
                    WHERE estado = 'VALIDO'
                ) as validos,
                COUNT(*) FILTER (
                    WHERE estado = 'EXISTENTE'
                ) as existentes,
                COUNT(*) FILTER (
                    WHERE estado = 'ERROR'
                ) as errores
            ")
            ->first();

        return [
            'total' => (int) $resultado->total,
            'validos' => (int) $resultado->validos,
            'existentes' => (int) $resultado->existentes,
            'errores' => (int) $resultado->errores,
        ];
    }

    public function puedeGuardar(): bool
    {
        if (! $this->importacionId) {
            return false;
        }

        return ImportacionRegistro::query()
            ->where(
                'importacion_id',
                $this->importacionId
            )
            ->where(
                'estado',
                'VALIDO'
            )
            ->where(
                'seleccionado',
                true
            )
            ->exists();
    }

    public function guardar(
        ImportacionService $service
    ): void {
        if (! $this->importacionId) {
            Notification::make()
                ->title('Importación no encontrada')
                ->body('No existe una importación para guardar.')
                ->danger()
                ->send();
            return;
        }

        if (! $this->puedeGuardar()) {
            Notification::make()
                ->title('No hay registros para guardar')
                ->body(
                    'Debe existir al menos un registro válido seleccionado.'
                )
                ->warning()
                ->send();
            return;
        }
        try {
            $service->guardarSeleccionados(
                $this->importacionId
            );

            Notification::make()
                ->title('Importación completada')
                ->body(
                    'Los registros seleccionados fueron guardados correctamente.'
                )
                ->success()
                ->send();

            /*
            * Refrescar la tabla.
            */
            $this->resetTable();

        } catch (\Throwable $e) {

            report($e);

            Notification::make()
                ->title('Error al guardar')
                ->body(
                    'No fue posible guardar los registros seleccionados.'
                )
                ->danger()
                ->send();
        }
    }
}
