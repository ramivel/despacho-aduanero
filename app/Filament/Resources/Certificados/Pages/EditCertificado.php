<?php

namespace App\Filament\Resources\Certificados\Pages;

use App\Filament\Resources\Certificados\CertificadoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCertificado extends EditRecord
{
    protected static string $resource = CertificadoResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterSave(): void
    {
        Notification::make()
            ->title('Certificado actualizado')
            ->body("Se actualizó correctamente el registro")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
