<?php

namespace App\Filament\Resources\Certificados\Tables;

use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;
use App\Models\Certificado;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class CertificadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('codigo_certificado')
                    ->searchable(),

                TextColumn::make('fecha_emision_certificado')
                    ->dateTime('d/m/Y H:i')
                    ->searchable(),

                TextColumn::make('tipo_solicitud')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->tooltip('Ver Items')
                    ->counts([
                        'items' => fn ($query) =>
                            $query->where('activo', true),
                    ])
                    ->sortable()
                    ->url(
                        fn (Certificado $record): string =>
                            CertificadoItemResource::getUrl(
                                'index',
                                [
                                    'certificado' => $record->uuid,
                                ],
                            )
                    )
                    ->color('info')
                    ->weight('bold'),

            ])

            ->recordUrl(null)

            ->defaultPaginationPageOption(500)

            ->paginationPageOptions([
                500,
                1000,
            ])

            ->defaultSort(
                'fecha_emision_certificado',
                'desc'
            )

            ->striped()

            ->recordActions([

                EditAction::make()
                    ->color('gray'),

                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar certificado')
                    ->modalDescription(
                        fn (Certificado $record): string =>
                            '¿Está seguro de eliminar el certificado "' .
                            $record->codigo_certificado .
                            '" y sus ítems?'
                    )
                    ->modalCancelActionLabel('No, Cancelar')
                    ->modalSubmitActionLabel('Sí, Eliminar')
                    ->action(function (Certificado $record): void {

                        DB::transaction(function () use ($record): void {

                            $datosAnteriores = $record->toArray();

                            $record->update([
                                'activo' => false,
                            ]);

                            $record->items()->update([
                                'activo' => false,
                            ]);

                            app(
                                \App\Services\AuditoriaService::class
                            )->registrar(
                                accion: 'DELETE',
                                registro: $record,
                                datosAnteriores: $datosAnteriores,
                                datosNuevos: $record
                                    ->fresh()
                                    ->toArray(),
                            );
                        });

                        Notification::make()
                            ->title('Certificado eliminado')
                            ->body(
                                'El certificado y sus ítems fueron eliminados.'
                            )
                            ->success()
                            ->send();
                    }),

            ]);
    }
}
