<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::admin-panels.auth.dashboard')
        ->name('dashboard');
    Route::livewire('users', 'pages::admin-panels.users.index')
        ->name('users');
    Route::livewire('orders', 'pages::admin-panels.orders.index')
        ->name('orders');
    Route::livewire('customers', 'pages::admin-panels.customers.index')
        ->name('customers');
    Route::livewire('categories', 'pages::admin-panels.categories.index')
        ->name('categories');
    Route::livewire('products', 'pages::admin-panels.products.index')
        ->name('products');
    Route::livewire('tables', 'pages::admin-panels.tables.index')
        ->name('tables');
    Route::livewire('reports', 'pages::admin-panels.reports.index')
        ->name('reports');
});
