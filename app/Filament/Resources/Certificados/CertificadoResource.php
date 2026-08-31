<?php

namespace App\Filament\Resources\Certificados;

use App\Filament\Resources\Certificados\Pages\CreateCertificado;
use App\Filament\Resources\Certificados\Pages\EditCertificado;
use App\Filament\Resources\Certificados\Pages\ListCertificados;
use App\Filament\Resources\Certificados\Pages\ViewCertificado;
use App\Filament\Resources\Certificados\Schemas\CertificadoForm;
use App\Filament\Resources\Certificados\Tables\CertificadosTable;
use App\Models\Certificado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificadoResource extends Resource
{
    protected static ?string $model = Certificado::class;

    protected static ?string $navigationLabel = 'Certificados';

    protected static ?string $modelLabel = 'Certificado';

    protected static ?string $pluralModelLabel = 'Certificados';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::DocumentDuplicate;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CertificadoForm::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('activo', true);
    }

    public static function table(Table $table): Table
    {
        return CertificadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificados::route('/'),

            'create' => CreateCertificado::route('/create'),

            'view' => ViewCertificado::route('/{record}'),

            'edit' => EditCertificado::route('/{record}/edit'),
        ];
    }
}
