<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\AuditoriaService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        app(AuditoriaService::class)->registrar(
            'INSERT',
            $this->record,
            null,
            [
                'name' => $this->record->name,
                'email' => $this->record->email,
                'activo' => $this->record->activo,
            ],
        );
        Notification::make()
            ->title('Usuario creado')
            ->body("Se inserto correctamente el registro")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }
}
