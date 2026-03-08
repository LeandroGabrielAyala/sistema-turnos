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