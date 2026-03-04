<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use App\Models\ObraSocial;

class ObraSocialResource extends Resource
{
    protected static ?string $model = ObraSocial::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('alias')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('nombre')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('alias')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth('lg'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ObraSocialResource\Pages\ListObraSocials::route('/'),
            'create' => \App\Filament\Resources\ObraSocialResource\Pages\CreateObraSocial::route('/create'),
            'edit' => \App\Filament\Resources\ObraSocialResource\Pages\EditObraSocial::route('/{record}/edit'),
        ];
    }
}