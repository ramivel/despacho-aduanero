<?php

namespace App\Filament\Resources\Certificados\Resources\CertificadoItems\Schemas;

use App\Models\CertificadoItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificadoItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulario de Registro')
                    ->description('Los campos marcados con * son obligatorios.')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('numero_item')
                            ->label('Nro. Item')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(999999),
                        TextInput::make('registro_sanitario')
                            ->label('Registro Sanitario')
                            ->maxLength(255),
                        TextInput::make('nro_lote')
                            ->label('Nro. Lote')
                            ->maxLength(255),
                        TextInput::make('cantidad_medicamento')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01'),
                        DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de Vencimiento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d'),
                        Select::make('evaluacion_item')
                            ->label('Evaluación del Item')
                            ->options(
                                fn (): array =>
                                    CertificadoItem::query()
                                        ->whereNotNull(
                                            'evaluacion_item'
                                        )
                                        ->where(
                                            'evaluacion_item',
                                            '<>',
                                            ''
                                        )
                                        ->distinct()
                                        ->orderBy(
                                            'evaluacion_item'
                                        )
                                        ->pluck(
                                            'evaluacion_item'
                                        )
                                        ->toArray()
                            )
                            ->searchable()
                            ->preload(),
                        Textarea::make('producto')
                            ->label('Producto')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
