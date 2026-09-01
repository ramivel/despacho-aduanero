<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\AuditoriaService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class UsersTable
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
                TextColumn::make('name')
                    ->label('Nombre Completo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->defaultPaginationPageOption(500)
            ->paginationPageOptions([
                500,
                1000,
            ])
            ->defaultSort('activo', 'desc')
            ->defaultSort('name', 'asc')
            ->striped()
            ->recordActions([
                Action::make('cambiarEstado')
                    ->label(fn (User $record): string => $record->activo ? 'Desactivar' : 'Activar')
                    ->icon(fn (User $record): string => $record->activo
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn (User $record): string => $record->activo ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => $record->activo
                        ? 'Desactivar usuario'
                        : 'Activar usuario')
                    ->modalDescription(fn (User $record): string => $record->activo
                        ? "¿Está seguro de desactivar al usuario {$record->name}?"
                        : "¿Está seguro de activar al usuario {$record->name}?")
                    ->modalSubmitActionLabel(fn (User $record): string => $record->activo
                        ? 'Desactivar'
                        : 'Activar')
                    ->action(function (User $record): void {
                        $activoAnterior = $record->activo;
                        $record->activo = ! $record->activo;
                        $record->save();
                        app(AuditoriaService::class)->registrar(
                            'UPDATE',
                            $record,
                            [
                                'activo' => $activoAnterior,
                            ],
                            [
                                'activo' => $record->activo,
                            ],
                        );
                        Notification::make()
                            ->title($record->activo ? 'Usuario activado' : 'Usuario desactivado')
                            ->body(
                                $record->activo
                                    ? "El usuario {$record->name} fue activado correctamente."
                                    : "El usuario {$record->name} fue desactivado correctamente."
                            )
                            ->icon(
                                $record->activo
                                    ? 'heroicon-o-check-circle'
                                    : 'heroicon-o-x-circle'
                            )
                            ->success()
                            ->send();
                    }),
                Action::make('cambiarContrasena')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255)
                            ->same('password_confirmation')
                            ->validationMessages([
                                'same' => 'Las contraseñas no coinciden.',
                            ])
                            ->autocomplete('new-password'),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255)
                            ->autocomplete('new-password'),
                    ])
                    ->modalHeading('Cambiar contraseña')
                    ->modalDescription(
                        fn (User $record): string =>
                            "Cambiar la contraseña del usuario {$record->email}."
                    )
                    ->modalSubmitActionLabel('Cambiar contraseña')
                    ->action(function (User $record, array $data): void {
                        $record->password = $data['password'];
                        $record->save();
                        app(AuditoriaService::class)->registrar(
                            'UPDATE',
                            $record,
                            [
                                'password' => '[OCULTA]',
                            ],
                            [
                                'password' => '[ACTUALIZADA]',
                            ],
                        );
                        Notification::make()
                            ->title('Contraseña actualizada')
                            ->body(
                                "La contraseña del usuario {$record->email} fue actualizada correctamente."
                            )
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Editar'),
            ]);
    }
}
