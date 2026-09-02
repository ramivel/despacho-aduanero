<?php

namespace App\Filament\Resources\Importacions;

use App\Enums\NavigationGroupEnum;
use App\Filament\Resources\Importacions\Pages\CreateImportacion;
use App\Filament\Resources\Importacions\Pages\EditImportacion;
use App\Filament\Resources\Importacions\Pages\ImportarArchivo;
use App\Filament\Resources\Importacions\Pages\ListImportacions;
use App\Filament\Resources\Importacions\Schemas\ImportacionForm;
use App\Filament\Resources\Importacions\Tables\ImportacionsTable;
use App\Models\Importacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ImportacionResource extends Resource
{
    protected static ?string $model = Importacion::class;
    protected static ?string $navigationLabel = 'Importar Registros';
    protected static ?string $modelLabel = 'Importación';
    protected static ?string $pluralModelLabel = 'Importaciones';
    protected static string|UnitEnum|null $navigationGroup = NavigationGroupEnum::DESPACHOS->value;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static ?string $recordTitleAttribute = 'nombre_archivo';

    public static function form(Schema $schema): Schema
    {
        return ImportacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImportacionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImportacions::route('/'),
            'create' => CreateImportacion::route('/create'),
            'edit' => EditImportacion::route('/{record}/edit'),
            'importar' => ImportarArchivo::route('/importar-archivo'),
        ];
    }
}
