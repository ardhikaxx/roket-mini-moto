<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    $products = \App\Models\Product::where('is_active', true)->where('show_on_landing', true)->get();
    return view('home', compact('products'));
})->name('home');

Route::get('/produk/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    return view('detail', compact('product'));
})->name('produk.detail');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    // ADMIN & KEPALA TOKO
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard');
        Route::resource('products', \App\Http\Controllers\ProductController::class);
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);
        Route::resource('stores', \App\Http\Controllers\StoreController::class);
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/approve', [\App\Http\Controllers\ReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [\App\Http\Controllers\ReportController::class, 'reject'])->name('reports.reject');
    });

    // KEPALA TOKO
    Route::prefix('kepalatoko')->name('kepalatoko.')->group(function () {
        Route::get('/dashboard', function() { return view('kepalatoko.dashboard'); })->name('dashboard');
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/approve', [\App\Http\Controllers\ReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [\App\Http\Controllers\ReportController::class, 'reject'])->name('reports.reject');
    });
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', function() { return view('karyawan.dashboard'); })->name('dashboard');
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [\App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    });
});
