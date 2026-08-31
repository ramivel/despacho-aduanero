<?php

namespace App\Filament\Resources\Certificados\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CertificadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulario de Registro')
                    ->description('Los campos marcados con * son obligatorios.')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('codigo_certificado')
                            ->required()
                            ->maxLength(100),
                        Select::make('tipo_solicitud')
                            ->label('Tipo de Solicitud')
                            ->options(
                                fn () => \App\Models\Certificado::query()
                                    ->whereNotNull('tipo_solicitud')
                                    ->where('tipo_solicitud', '!=', '')
                                    ->distinct()
                                    ->orderBy('tipo_solicitud')
                                    ->pluck('tipo_solicitud', 'tipo_solicitud')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('nit')
                            ->maxLength(50),
                        TextInput::make('nombre_empresa')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        TextInput::make('tipo_producto')
                            ->maxLength(255),
                        Toggle::make('producto_refrigerado'),
                        TextInput::make('procedencia')
                            ->maxLength(100),
                        DatePicker::make('fecha_solicitud')
                            ->required(),
                        DateTimePicker::make('fecha_emision_certificado')
                            ->required(),
                        Textarea::make('uso')
                            ->columnSpanFull(),
                        TextInput::make('proveedor')
                            ->columnSpanFull()
                            ->maxLength(255),
                        TextInput::make('nro_factura')
                            ->maxLength(100),
                        TextInput::make('monto_factura')
                            ->numeric(),
                        Textarea::make('observacion')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
