<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Actions\Action; // ✅ IMPORTANTE

class CalendarioTurnos extends Page
{
    protected static string $view = 'filament.pages.calendario-turnos-page';
    protected static ?string $title = 'Calendario de Turnos';
    protected static ?string $navigationGroup = 'Agenda';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'turnos/calendario';
}