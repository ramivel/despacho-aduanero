<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulario de Registro')
                    ->description('Los campos marcados con * son obligatorios.')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('name')
                            ->placeholder('Ingrese el nombre del usuario'),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->autocomplete('email')
                            ->maxLength(255)
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true,
                            )
                            ->placeholder('usuario@empresa.com'),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->placeholder('Ingrese una contraseña segura de al menos 8 caracteres'),
                    ]),
            ]);
    }
}
