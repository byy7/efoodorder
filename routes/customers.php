<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/', 'pages::customer-panels.main')
        ->name('home');

    Route::livewire('customer-registrations/{type}', 'pages::customer-panels.registration')
        ->name('registrations');

    Route::livewire('customer-orders/{type}/{id}', 'pages::customer-panels.order')
        ->name('customer_orders');
});
