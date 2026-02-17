<?php

use App\Http\Controllers\XenditWebhookController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::customer-panels.main')
    ->name('home');

Route::livewire('customer-registrations/{type}', 'pages::customer-panels.registration')
    ->name('registrations');

Route::livewire('customer-orders/{type}/{customerId}', 'pages::customer-panels.order')
    ->name('customer_orders');

Route::livewire('payment/success/{orderId}', 'pages::customer-panels.payment-result.payment-success')
    ->name('customer.payment.success');

Route::livewire('payment/failed/{orderId}', 'pages::customer-panels.payment-result.payment-failed')
    ->name('customer.payment.failed');

Route::post('webhooks/xendit', XenditWebhookController::class)
    ->name('webhooks.xendit');
