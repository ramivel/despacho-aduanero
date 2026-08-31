<?php

namespace App\Filament\Resources\Importacions\Pages;

use App\Filament\Resources\Importacions\ImportacionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListImportacions extends ListRecords
{
    protected static string $resource = ImportacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importar')
                ->label('Importar Archivo') // Texto del botón
                ->color('primary') // Color (primary, success, danger, info, gray)
                ->icon('heroicon-o-arrow-down-on-square') // Icono opcional
                ->url(
                    ImportarArchivo::getUrl()
                ),
        ];
    }
}
