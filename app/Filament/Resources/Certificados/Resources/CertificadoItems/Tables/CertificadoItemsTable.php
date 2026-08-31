<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems\Tables;

use App\Models\CertificadoItem;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class CertificadoItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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

            ->recordUrl(null)

            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('gray'),

                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar ítem')
                    ->modalDescription(
                        fn (CertificadoItem $record): string =>
                            '¿Está seguro de eliminar el ítem Nro. ' .
                            $record->numero_item .
                            '?'
                    )
                    ->modalCancelActionLabel('No, Cancelar')
                    ->modalSubmitActionLabel('Sí, Eliminar')
                    ->action(
                        function (
                            CertificadoItem $record
                        ): void {

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
                                ->title('Ítem eliminado')
                                ->body(
                                    'El ítem fue deshabilitado correctamente.'
                                )
                                ->success()
                                ->send();
                        }
                    ),
            ])

            ->defaultSort(
                'numero_item',
                'asc'
            )

            ->striped()

            ->paginated([
                25,
                50,
                100,
                500,
            ]);
    }
}
