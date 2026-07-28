<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $products = Product::where('is_active', true)->get();
    return response()->view('sitemap', compact('products'))->header('Content-Type', 'application/xml');
});