<?php

namespace App\Filament\Resources\Importacions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Importacion;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Certificado;
use App\Models\CertificadoItem;
use Illuminate\Support\Facades\DB;

class ImportacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nro')
                    ->sortable()
                    ->rowIndex()
                    ->searchable(),
                TextColumn::make('fecha_importacion')
                    ->label('Fecha de Importación')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_certificados')
                    ->label('Total Certificados')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_items')
                    ->label('Total Items')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('observacion')
                    ->label('Observación')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
            ])
            ->recordUrl(null)
            ->defaultPaginationPageOption(500)
            ->paginationPageOptions([
                500,
                1000,
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Anular importación')
                    ->modalCancelActionLabel('No, Cancelar')
                    ->modalSubmitActionLabel('Sí, Anular')
                    ->modalDescription(
                        fn (Importacion $record) =>
                            '¿Está seguro de anular la importación Nro. ' .
                            $record->id .
                            '?'
                    )
                    ->action(function (Importacion $record) {
                        DB::transaction(function () use ($record) {
                            $datosAnteriores = $record->toArray();
                            $certificadoIds = Certificado::where(
                                'importacion_id',
                                $record->id
                            )->pluck('id');
                            Certificado::whereIn('id', $certificadoIds)
                                ->update([
                                    'activo' => false,
                                ]);
                            CertificadoItem::whereIn(
                                'certificado_id',
                                $certificadoIds
                            )->update([
                                'activo' => false,
                            ]);
                            $record->update([
                                'estado' => 'ANULADA',
                            ]);
                            // Registrar auditoría
                            app(\App\Services\AuditoriaService::class)->registrar(
                                accion: 'UPDATE',
                                registro: $record,
                                datosAnteriores: $datosAnteriores,
                                datosNuevos: $record->fresh()->toArray(),
                            );
                        });
                        Notification::make()
                            ->title('Importación anulada')
                            ->body(
                                'La importación y sus datos relacionados fueron deshabilitados.'
                            )
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn (Importacion $record): bool =>
                            $record->estado !== 'ANULADA'
                    ),
            ]);
    }
}
