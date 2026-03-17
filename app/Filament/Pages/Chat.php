<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Chat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.chat';

    protected static ?string $navigationLabel = 'Chat';

    protected static ?int $navigationSort = 10;

    protected ?string $maxContentWidth = 'full';

    protected static bool $shouldRegisterNavigationBadgeListeners = true;

    protected $listeners = [
        'update-chat-badge' => '$refresh',
    ];
    
    public static function getNavigationBadge(): ?string
    {
        $count = \App\Models\ChatMessage::whereNull('read_at')
            ->where('sender_id', '!=', auth()->id())
            ->count();

        return $count > 0 ? $count : null;
    }

}