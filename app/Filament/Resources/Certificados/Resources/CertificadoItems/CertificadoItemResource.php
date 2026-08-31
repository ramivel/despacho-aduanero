<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems;

use App\Filament\Resources\Certificados\CertificadoResource;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages\CreateCertificadoItem;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages\EditCertificadoItem;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\Pages\ListCertificadoItems;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\Schemas\CertificadoItemForm;
use App\Filament\Resources\Certificados\Resources\CertificadoItems\Tables\CertificadoItemsTable;
use App\Models\Certificado;
use App\Models\CertificadoItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificadoItemResource extends Resource
{
    protected static ?string $model = CertificadoItem::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource =
        CertificadoResource::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getCertificado(): Certificado
    {
        $uuid = request()->route('certificado');

        return Certificado::query()
            ->where('activo', true)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public static function form(Schema $schema): Schema
    {
        return CertificadoItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificadoItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificadoItems::route('/'),

            'create' => CreateCertificadoItem::route('/create'),

            'edit' => EditCertificadoItem::route('/{record}/edit'),
        ];
    }
}
