<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function getTitle(): string
    {
        return 'Iniciar sesión';
    }

    public function getHeading(): string
    {
        return 'Sistema de Gestión de Clínica';
    }

    public function getSubheading(): ?string
    {
        return 'Ingrese sus credenciales para acceder';
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label('Correo electrónico')
            ->placeholder('ejemplo@clinica.com')
            ->required();
    }

    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label('Contraseña')
            ->password()
            ->required();
    }

    public function getAuthenticateFormActionLabel(): string
    {
        return 'Ingresar';
    }
}