<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    // Ideally this would fetch dynamic products
    $products = \App\Models\Product::where('is_active', true)->where('show_on_landing', true)->get();
    return view('home', compact('products'));
})->name('home');

Route::get('/produk/{id}', function ($id) {
    // Actually using the real DB for detail if needed, but keeping simple for now
    return view('detail');
})->name('produk.detail');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    // ADMIN & KEPALA TOKO
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard');
        
        // Products
        Route::get('/products', function() { return view('admin.products.index'); })->name('products.index');
        
        // Stores
        Route::get('/stores', function() { return view('admin.stores.index'); })->name('stores.index');
        
        // Users
        Route::get('/users', function() { return view('admin.users.index'); })->name('users.index');
        
        // Reports
        Route::get('/reports', function() { return view('admin.reports.index'); })->name('reports.index');
    });

    // KARYAWAN
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', function() { return view('karyawan.dashboard'); })->name('dashboard');
        Route::get('/reports', function() { return view('karyawan.reports.index'); })->name('reports.index');
    });
});
