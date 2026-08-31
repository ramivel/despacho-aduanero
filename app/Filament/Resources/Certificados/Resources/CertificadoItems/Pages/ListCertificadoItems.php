<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages;

use App\Filament\Resources\Certificados\CertificadoResource;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\CertificadoItemResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCertificadoItems extends ListRecords
{
    protected static string $resource =
        CertificadoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('atras')
                ->label('Atrás')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(
                    CertificadoResource::getUrl('index')
                ),

            Action::make('nuevoItem')
                ->label('Nuevo item')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(
                    fn (): string =>
                        CertificadoItemResource::getUrl(
                            'create',
                            [
                                'certificado' =>
                                    CertificadoItemResource
                                        ::getCertificado()
                                        ->uuid,
                            ]
                        )
                ),
        ];
    }
}
