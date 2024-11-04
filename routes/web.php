<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManageController;

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

// master product
Route::get('/product', [App\Http\Controllers\ProductController::class, 'index'])->name('product');

// master users
Route::get('/admin/users', [UserManageController::class, 'index'])->name('admin.users');
Route::get('/admin/users/create', [UserManageController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users', [UserManageController::class, 'store'])->name('admin.users.store');
Route::get('/admin/users/{id}/edit', [UserManageController::class, 'edit'])->name('admin.users.edit');
Route::put('/admin/users/{id}', [UserManageController::class, 'update'])->name('admin.users.update');
Route::delete('/admin/users/{id}', [UserManageController::class, 'destroy'])->name('admin.users.destroy');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});