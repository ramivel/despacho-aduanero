<?php

namespace App\Filament\Resources\Certificados\Pages;

use App\Filament\Resources\Certificados\CertificadoResource;
use App\Models\CertificadoItem;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;

class ViewCertificado extends ViewRecord
{
    protected static string $resource = CertificadoResource::class;
    protected string $view = 'filament.resources.certificados.pages.view-certificado';

    public function getTitle(): string
    {
        return $this->record->codigo_certificado;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('atras')
                ->label('Atrás')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(
                    CertificadoResource::getUrl('index')
                ),
            Action::make('nuevoItem')
                ->label('Nuevo item')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(
                    fn (): string =>
                        CertificadoItemResource::getUrl(
                            'create',
                            [
                                'certificado' => $this->record->uuid,
                            ]
                        )
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CertificadoItem::query()
                    ->where(
                        'certificado_id',
                        $this->record->id
                    )
                    ->where(
                        'activo',
                        true
                    )
            )
            ->columns([
                TextColumn::make('numero_item')
                    ->label('Nro')
                    ->rowIndex()
                    ->sortable(),
                TextColumn::make('producto')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('registro_sanitario')
                    ->label('Registro Sanitario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nro_lote')
                    ->label('Nro. Lote')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('evaluacion_item')
                    ->label('Evaluación')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
            ])
            ->defaultSort(
                'numero_item',
                'asc'
            )
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->url(
                        fn (CertificadoItem $record): string =>
                            CertificadoItemResource::getUrl(
                                'edit',
                                [
                                    'certificado' => $this->record->uuid,
                                    'record' => $record,
                                ]
                            )
                    ),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar item')
                    ->modalDescription(
                        fn (CertificadoItem $record): string =>
                            '¿Está seguro de eliminar el item Nro. ' .
                            $record->numero_item .
                            ' del certificado "' .
                            $this->record->codigo_certificado .
                            '"?'
                    )
                    ->modalCancelActionLabel('No, Cancelar')
                    ->modalSubmitActionLabel('Sí, Eliminar')
                    ->action(
                        function (CertificadoItem $record): void {
                            DB::transaction(
                                function () use ($record): void {
                                    $datosAnteriores =
                                        $record->toArray();
                                    $record->update([
                                        'activo' => false,
                                    ]);
                                    app(
                                        \App\Services\AuditoriaService::class
                                    )->registrar(
                                        accion: 'DELETE',
                                        registro: $record,
                                        datosAnteriores:
                                            $datosAnteriores,
                                        datosNuevos:
                                            $record
                                                ->fresh()
                                                ->toArray(),
                                    );
                                }
                            );
                            Notification::make()
                                ->title('Item eliminado')
                                ->body(
                                    'El item fue deshabilitado correctamente.'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])
            ->striped()
            ->paginated([
                25,
                50,
                100,
                500,
            ]);
    }
}
