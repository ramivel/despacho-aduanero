<?php

namespace App\Filament\Resources\Importacions\Pages;

use App\Filament\Resources\Importacions\ImportacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImportacion extends EditRecord
{
    protected static string $resource = ImportacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
