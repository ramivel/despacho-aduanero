<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Models\Certificado;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

class BuscadorRegistros extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Buscador Registros';
    protected static ?string $title = 'Buscador de Registros';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?int $navigationSort = 20;
    protected string $view = 'filament.pages.buscador-registros';
    public ?array $data = [];
    public bool $buscado = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filtros de búsqueda')
                    ->schema([
                        TextInput::make('texto')
                            ->label('Texto a buscar')
                            ->placeholder('Escriba el Texto a Buscar')
                            ->extraInputAttributes(['onInput' => 'this.value = this.value.toUpperCase()'])
                            ->maxLength(255)
                            ->required(),
                        Select::make('campo')
                            ->label('Buscar por')
                            ->options([
                                'codigo_certificado' => 'Código Certificado',
                                'fecha_emision_certificado' => 'Fecha Emisión Certificado (DD/MM/YYYY)',
                                'nombre_empresa' => 'Nombre Empresa',
                                'registro_sanitario' => 'Registro Sanitario',
                                'nro_lote' => 'Número de Lote',
                            ])
                            ->default('codigo_certificado')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }
    public function buscar(): void
    {
        $texto = mb_strtoupper(trim($this->data['texto'] ?? ''));
        if ($texto === '') {
            \Filament\Notifications\Notification::make()
                ->title('Ingrese un criterio de búsqueda')
                ->body(
                    'Debe escribir el texto que desea buscar.'
                )
                ->warning()
                ->send();
            return;
        }
        $this->buscado = true;
        $this->resetTable();
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(

                Certificado::query()
                    ->where('activo', true)
                    ->with([
                        'items' => fn ($query) =>
                            $query->where('activo', true),
                    ])
                    ->when(
                        ! $this->buscado,
                        fn ($query) => $query->whereRaw('1 = 0')
                    )
                    ->when(
                        $this->buscado,
                        function ($query) {
                            $texto = mb_strtoupper(trim($this->data['texto'] ?? ''));
                            $campo = $this->data['campo'] ?? 'codigo_certificado';
                            if ($texto === '')
                                return;
                            switch ($campo) {
                                case 'codigo_certificado':
                                    $query->where(
                                        'codigo_certificado',
                                        'ILIKE',
                                        "%{$texto}%"
                                    );
                                    break;
                                case 'fecha_emision_certificado':
                                    try {
                                        $fecha = \Carbon\Carbon::createFromFormat(
                                            'd/m/Y',
                                            $texto
                                        );
                                        if ($fecha->format('d/m/Y') !== $texto)
                                            throw new \Exception('Formato de fecha inválido');
                                        $query->whereDate(
                                            'fecha_emision_certificado',
                                            $fecha->format('Y-m-d')
                                        );
                                    } catch (\Throwable) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Formato de fecha incorrecto')
                                            ->body('La fecha debe tener el formato DD/MM/YYYY. Ejemplo: 18/08/2026.')
                                            ->danger()
                                            ->send();
                                        $query->whereRaw(
                                            '1 = 0'
                                        );
                                    }
                                    break;
                                case 'nombre_empresa':
                                    $query->where(
                                        'nombre_empresa',
                                        'ILIKE',
                                        "%{$texto}%"
                                    );
                                    break;
                                case 'registro_sanitario':
                                    $query->whereHas(
                                        'items',
                                        fn ($itemQuery) =>
                                            $itemQuery
                                                ->where('activo', true)
                                                ->where(
                                                    'registro_sanitario',
                                                    'ILIKE',
                                                    "%{$texto}%"
                                                )
                                    );
                                    break;
                                case 'nro_lote':
                                    $query->whereHas(
                                        'items',
                                        fn ($itemQuery) =>
                                            $itemQuery
                                                ->where('activo', true)
                                                    ->where(
                                                    'nro_lote',
                                                    'ILIKE',
                                                    "%{$texto}%"
                                                )
                                    );
                                    break;
                            }
                        }
                    )
            )
            ->columns([
                TextColumn::make('codigo_certificado')
                    ->label('Código Certificado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_emision_certificado')
                    ->label('Fecha Emisión Certificado')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('nombre_empresa')
                    ->label('Nombre Empresa')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts([
                        'items' => fn ($query) =>
                            $query->where('activo', true),
                    ]),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('DATOS DEL CERTIFICADO')
                    ->modalWidth('7xl')
                    ->modalAlignment(Alignment::Start)
                    ->modalContent(
                        fn (Certificado $record) =>
                            view(
                                'filament.pages.buscador-registros.detalle-certificado',
                                [
                                    'certificado' => $record,
                                ]
                            )
                    )
                    ->modalFooterActions([
                        Action::make('cerrar')
                            ->label('Cerrar')
                            ->color('gray')
                            ->close(),
                    ])
                    ->modalFooterActionsAlignment(Alignment::End),
            ])
            ->defaultSort(
                'fecha_emision_certificado',
                'desc'
            )
            ->defaultPaginationPageOption(500)
            ->paginationPageOptions([
                500,
                1000,
            ]);
    }
}
