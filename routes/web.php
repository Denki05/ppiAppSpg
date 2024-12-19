<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManageController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\ApiConsumerController;

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
Route::get('/product', [ApiConsumerController::class, 'getItemsProducts'])->name('product');
Route::get('/brands', [ApiConsumerController::class, 'getItemsBrands'])->name('brands');

// Route for fetching products based on brand ID (for dependent dropdown)
Route::get('/product/brand/{brand}', [ApiConsumerController::class, 'getProductsByBrand']);

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
Route::get('/admin/users/{provinsiID}/getKabupaten', [UserManageController::class, 'getKabupaten'])->name('admin.users.getKabupaten');
Route::get('/admin/users/{kabupatenID}/getKecamatan', [UserManageController::class, 'getKecamatan'])->name('admin.users.getKecamatan');
Route::get('/admin/users/{kecamatanID}/getKelurahan', [UserManageController::class, 'getKelurahan'])->name('admin.users.getKelurahan');

// Penjualan
Route::get('/penjualan', [SalesController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/create_senses', [SalesController::class, 'create_senses'])->name('penjualan.create_senses');
Route::get('/penjualan/create_gcf', [SalesController::class, 'create_gcf'])->name('penjualan.create_gcf');
Route::post('/penjualan/store', [SalesController::class, 'store'])->name('penjualan.store');
Route::delete('/penjualan/{id}', [SalesController::class, 'destroy'])->name('penjualan.destroy');
Route::get('/penjualan/checkCustomerDOM', [SalesController::class, 'checkCustomerDOM'])->name('penjualan.checkCustomerDOM');
Route::get('/penjualan/checkCustomerOUTDOM', [SalesController::class, 'checkCustomerOUTDOM'])->name('penjualan.checkCustomerOUTDOM');

// Customer
Route::get('/master/customer', [CustomerController::class, 'index'])->name('master.customer.index');
Route::get('/master/customer/create', [CustomerController::class, 'create'])->name('master.customer.create');
Route::post('/master/customer/store', [CustomerController::class, 'store'])->name('master.customer.store');
Route::get('/master/customer/show/{id}', [CustomerController::class, 'show'])->name('master.customer.show');
Route::get('/master/customer/edit/{id}', [CustomerController::class, 'edit'])->name('master.customer.edit');
Route::put('/master/customer/update/{id}', [CustomerController::class, 'update'])->name('master.customer.update');
Route::delete('/master/customer/destroy/{id}', [CustomerController::class, 'destroy'])->name('master.customer.destroy');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});