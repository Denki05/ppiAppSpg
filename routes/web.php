<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// GET API
Route::get('/product', [App\Http\Controllers\ApiConsumerController::class, 'getItemsProducts'])->name('product');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// master route
Route::get('/product', [App\Http\Controllers\ProductController::class, 'index'])->name('product');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});