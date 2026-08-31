<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages;

use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificadoItem extends EditRecord
{
    protected static string $resource = CertificadoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
