<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat-test', function () {
    return view('chat-test');
});

Route::get('/generate-password', function () {
    return Hash::make('123456');
});

Route::get('/chat/unread-count', function () {
    return \App\Models\ChatMessage::whereNull('read_at')
        ->where('sender_id', '!=', auth()->id())
        ->count();
})->middleware('auth');