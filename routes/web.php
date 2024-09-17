<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TempImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Category;

// Group routes under admin prefix
Route::group(['prefix' => 'admin'], function () {

    // Routes for guest (non-authenticated admin users)
    Route::group(['middleware' => 'guest:admin'], function () {
        // Admin authentication routes
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    // Routes for authenticated admin users
    Route::group(['middleware' => 'auth:admin'], function () {
        // Admin dashboard and logout routes
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // Category routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        // Route for generating slugs dynamically
        Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
  // web.php
        Route::post('/generate-slug', [CategoryController::class, 'generateSlug'])->name('generateSlug');
        Route::post('temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');


       //Route for subcategories
       Route::get('sub-category/create', [SubCategoryController::class, 'create'])->name('sub-category.create');
       Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-category.index');
    Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-category.store');
   // Route::resource('subcategories', SubCategoryController::class);

    Route::get('subcategories/{id}/edit', [SubCategoryController::class, 'edit'])->name('subcategories.edit');
    Route::put('subcategories/{id}', [SubcategoryController::class, 'update'])->name('subcategories.update');
    


// Route list for managing brands

Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
Route::get('/brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brands.update');
Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');

// Route list for Managing Product

Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // List all products
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // Show form to create a new product
Route::post('/products', [ProductController::class, 'store'])->name('products.store'); // Store a newly created product
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit'); // Show form to edit a product
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // Update an existing product
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy'); // Delete a product


    });
});