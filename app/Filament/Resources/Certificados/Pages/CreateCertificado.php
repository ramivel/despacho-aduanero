<?php

namespace App\Filament\Resources\Certificados\Pages;

use App\Filament\Resources\Certificados\CertificadoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\AuditoriaService;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;

class CreateCertificado extends CreateRecord
{
    protected static string $resource = CertificadoResource::class;

    protected function afterCreate(): void
    {
        app(AuditoriaService::class)->registrar(
            accion: 'INSERT',
            registro: $this->record,
            datosAnteriores: null,
            datosNuevos: $this->record->toArray(),
        );
        Notification::make()
            ->title('Certificado creado')
            ->body("Se inserto correctamente el registro")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }
    protected function getRedirectUrl(): string
    {
        return CertificadoItemResource::getUrl(
            'create',
            [
                'certificado' => $this->record->uuid,
            ]
        );
    }
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
