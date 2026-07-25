<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/produk/{id}', function ($id) {
    // The script inside detail.blade.php uses request()->route('id')
    return view('detail');
})->name('produk.detail');
