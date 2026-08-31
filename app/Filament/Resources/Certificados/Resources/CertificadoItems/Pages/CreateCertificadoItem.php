<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages;

use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCertificadoItem extends CreateRecord
{
    protected static string $resource =
        CertificadoItemResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $certificado =
            CertificadoItemResource::getCertificado();

        $data['certificado_id'] =
            $certificado->id;

        $data['activo'] = true;

        $data['editado'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        app(
            \App\Services\AuditoriaService::class
        )->registrar(
            accion: 'INSERT',
            registro: $this->record,
            datosAnteriores: null,
            datosNuevos: $this->record
                ->fresh()
                ->toArray(),
        );

        Notification::make()
            ->title('Item creado')
            ->body('Se insertó correctamente el registro.')
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        $certificado =
            CertificadoItemResource::getCertificado();

        return route(
            'filament.admin.resources.certificados.certificado-items.index',
            [
                'certificado' => $certificado->uuid,
            ]
        );
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
