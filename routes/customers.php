<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('main');
    })->name('main');
});

Route::livewire('register-customers/{type}', 'pages::customer-panels.registration')
    ->name('register_customers');
