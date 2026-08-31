<?php

namespace App\Filament\Resources\Auditorias;

use App\Filament\Resources\Auditorias\Pages\CreateAuditoria;
use App\Filament\Resources\Auditorias\Pages\EditAuditoria;
use App\Filament\Resources\Auditorias\Pages\ListAuditorias;
use App\Filament\Resources\Auditorias\Schemas\AuditoriaForm;
use App\Filament\Resources\Auditorias\Tables\AuditoriasTable;
use App\Models\Auditoria;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class AuditoriaResource extends Resource
{
    protected static ?string $model = Auditoria::class;
    protected static ?string $navigationLabel = 'Auditoria';
    protected static string|UnitEnum|null $navigationGroup = 'Administración';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AuditoriaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditoriasTable::configure($table);
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
            'index' => ListAuditorias::route('/'),
            'create' => CreateAuditoria::route('/create'),
            'edit' => EditAuditoria::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Nro.'),
                        TextEntry::make('created_at')
                            ->label('Fecha y Hora')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('usuario.name')
                            ->label('Usuario'),
                        TextEntry::make('ip')
                            ->label('Dirección IP'),
                        TextEntry::make('tabla')
                            ->label('Tabla'),
                        TextEntry::make('registro_id')
                            ->label('Registro'),
                        TextEntry::make('accion')
                            ->label('Acción')
                            ->badge(),
                        TextEntry::make('valor_anterior')
                            ->label('Valor Anterior')
                            ->formatStateUsing(
                                fn ($state) => $state
                                    ? json_encode(
                                        $state,
                                        JSON_PRETTY_PRINT |
                                        JSON_UNESCAPED_UNICODE
                                    )
                                    : 'Sin información'
                            )
                            ->columnSpanFull(),
                        TextEntry::make('valor_nuevo')
                            ->label('Valor Nuevo')
                            ->formatStateUsing(
                                fn ($state) => $state
                                    ? json_encode(
                                        $state,
                                        JSON_PRETTY_PRINT |
                                        JSON_UNESCAPED_UNICODE
                                    )
                                    : 'Sin información'
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
