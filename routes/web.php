<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
})->name('home');

require __DIR__.'/admins.php';
require __DIR__.'/customers.php';
require __DIR__.'/settings.php';
