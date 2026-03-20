<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;

class Login extends \Filament\Pages\Auth\Login
{

    /**
     * Campo usuario/email
     */
    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label('Correo electrónico')
            ->placeholder('ejemplo@clinica.com')
            ->required();
    }

    /**
     * Campo contraseña
     */
    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label('Contraseña')
            ->password()
            ->required();
    }
}