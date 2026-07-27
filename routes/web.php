<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RevenueController;

use Illuminate\Support\Facades\File;

// Image Serving Routes (No storage:link required)
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $paths = [
        public_path("uploads/{$folder}/{$filename}"),
        storage_path("uploads/{$folder}/{$filename}"),
        public_path("storage/{$folder}/{$filename}"),
        storage_path("app/public/{$folder}/{$filename}"),
    ];

    foreach ($paths as $path) {
        if (File::exists($path) && !File::isDirectory($path)) {
            $file = File::get($path);
            $type = File::mimeType($path) ?: 'image/jpeg';
            return response($file, 200)->header('Content-Type', $type);
        }
    }

    $fallback = public_path('assets/images/no-image.png');
    if (File::exists($fallback)) {
        return response(File::get($fallback), 200)->header('Content-Type', 'image/png');
    }
    abort(404);
})->where(['folder' => '[a-zA-Z0-9_-]+', 'filename' => '[a-zA-Z0-9_.-]+']);

Route::get('/uploads/{folder}/{filename}', function ($folder, $filename) {
    $paths = [
        public_path("uploads/{$folder}/{$filename}"),
        storage_path("uploads/{$folder}/{$filename}"),
        public_path("storage/{$folder}/{$filename}"),
        storage_path("app/public/{$folder}/{$filename}"),
    ];

    foreach ($paths as $path) {
        if (File::exists($path) && !File::isDirectory($path)) {
            $file = File::get($path);
            $type = File::mimeType($path) ?: 'image/jpeg';
            return response($file, 200)->header('Content-Type', $type);
        }
    }

    $fallback = public_path('assets/images/no-image.png');
    if (File::exists($fallback)) {
        return response(File::get($fallback), 200)->header('Content-Type', 'image/png');
    }
    abort(404);
})->where(['folder' => '[a-zA-Z0-9_-]+', 'filename' => '[a-zA-Z0-9_.-]+']);

// Public Routes
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

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Profile (all roles)
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/change-pin', [AuthController::class, 'profile'])->name('profile.change-pin');
    Route::post('/profile/change-pin', [AuthController::class, 'changePin'])->name('profile.change-pin');

    // Notifications (all roles)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin,kepala_toko')->group(function () {
        Route::get('/dashboard', function() { 
            $user = auth()->user();
            if ($user->isKepalaToko()) return redirect()->route('kepalatoko.dashboard');
            return view('admin.dashboard'); 
        })->name('dashboard');

        // Products (admin only)
        Route::resource('products', ProductController::class)->middleware('role:admin');
        Route::post('/products/{product}/activate', [ProductController::class, 'activate'])->name('products.activate')->middleware('role:admin');

        // Categories (admin only)
        Route::resource('categories', CategoryController::class)->middleware('role:admin');

        // Stores (admin only)
        Route::resource('stores', StoreController::class)->middleware('role:admin');

        // Users (admin only)
        Route::resource('users', UserController::class)->middleware('role:admin');
        Route::post('/users/{user}/reset-pin', [UserController::class, 'resetPin'])->name('users.reset-pin')->middleware('role:admin');

        // Reports (admin & kepala_toko)
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/approve', [ReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [ReportController::class, 'reject'])->name('reports.reject');
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy')->middleware('role:admin');

        // Omzet (admin only)
        Route::get('/omzet', [RevenueController::class, 'index'])->name('omzet')->middleware('role:admin');
        Route::get('/omzet/chart-data', [RevenueController::class, 'chartData'])->name('omzet.chart')->middleware('role:admin');
        Route::get('/omzet/export-excel', [RevenueController::class, 'exportExcel'])->name('omzet.export-excel')->middleware('role:admin');
        Route::get('/omzet/export-pdf', [RevenueController::class, 'exportPdf'])->name('omzet.export-pdf')->middleware('role:admin');

        // Audit Log (admin only)
        Route::get('/audit-log', function() {
            return view('audit_log.index');
        })->name('audit-log')->middleware('role:admin');
    });

    // Kepala Toko Dashboard (separate)
    Route::get('/kepalatoko/dashboard', function() {
        return view('kepalatoko.dashboard');
    })->name('kepalatoko.dashboard')->middleware('role:kepala_toko');

    // Karyawan Routes
    Route::prefix('karyawan')->name('karyawan.')->middleware('role:karyawan')->group(function () {
        Route::get('/dashboard', function() { return view('karyawan.dashboard'); })->name('dashboard');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/reports/images/{image}', function(\App\Models\SalesReportImage $image) {
            if ($image->salesReport->user_id !== auth()->id()) abort(403);
            \App\Helpers\FileUploadHelper::delete($image->image_path);
            $image->delete();
            return back()->with('success', 'Foto dihapus.');
        })->name('reports.delete-image');
    });
});
