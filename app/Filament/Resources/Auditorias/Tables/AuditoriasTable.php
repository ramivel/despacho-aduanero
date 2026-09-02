<?php

namespace App\Filament\Resources\Auditorias\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;

class AuditoriasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nro.')
                    ->rowIndex()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accion')
                    ->label('Acción')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tabla')
                    ->label('Tabla')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registro_id')
                    ->label('Registro')
                    ->sortable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable(),
            ])
            ->recordUrl(null)
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(500)
            ->paginationPageOptions([
                500,
                1000,
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Información de Auditoría')
                    ->modalFooterActions([
                        Action::make('cerrar')
                            ->label('Cerrar')
                            ->color('gray')
                            ->close(),
                    ])
                    ->modalFooterActionsAlignment(Alignment::End),
            ]);
    }
}
