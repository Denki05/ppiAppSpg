<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManageController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\Master\StockGaController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\Report\ReportJurnalDailyController;
use App\Http\Controllers\ApiConsumerController;
use App\Http\Controllers\HomeController;

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

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/getNotifData', [HomeController::class, 'getNotifData'])->name('getNotifData');
    Route::post('/unread_all_notif', [HomeController::class, 'unread_all_notif'])->name('unread_all_notif');
});

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
Route::get('/penjualan/review', [SalesController::class, 'review'])->name('penjualan.review');
Route::get('/penjualan/settle', [SalesController::class, 'settle'])->name('penjualan.settle');
Route::get('/penjualan', [SalesController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/create_senses', [SalesController::class, 'create_senses'])->name('penjualan.create_senses');
Route::get('/penjualan/create_gcf', [SalesController::class, 'create_gcf'])->name('penjualan.create_gcf');
Route::post('/penjualan/store', [SalesController::class, 'store'])->name('penjualan.store');
Route::get('/penjualan/edit/{id}', [SalesController::class, 'edit'])->name('penjualan.edit');
Route::put('/penjualan/update/{id}', [SalesController::class, 'update'])->name('penjualan.update');
Route::post('/penjualan/settel/{id}', [SalesController::class, 'settel'])->name('penjualan.settel');
Route::delete('/penjualan/{id}', [SalesController::class, 'destroy'])->name('penjualan.destroy');
Route::get('/penjualan/checkCustomerDOM', [SalesController::class, 'checkCustomerDOM'])->name('penjualan.checkCustomerDOM');
Route::get('/penjualan/checkCustomerOUTDOM', [SalesController::class, 'checkCustomerOUTDOM'])->name('penjualan.checkCustomerOUTDOM');
Route::get('/penjualan/cek_jurnal', [SalesController::class, 'cek_jurnal'])->name('penjualan.cek_jurnal');
Route::get('/penjualan/edit_settel/{id}', [SalesController::class, 'edit_settel'])->name('penjualan.edit_settel');
Route::put('/penjualan/update_settel/{id}', [SalesController::class, 'update_settel'])->name('penjualan.update_settel');

// Customer
Route::get('/master/customer', [CustomerController::class, 'index'])->name('master.customer.index');
Route::get('/master/customer/create', [CustomerController::class, 'create'])->name('master.customer.create');
Route::post('/master/customer/store', [CustomerController::class, 'store'])->name('master.customer.store');
Route::get('/master/customer/show/{id}', [CustomerController::class, 'show'])->name('master.customer.show');
Route::get('/master/customer/edit/{id}', [CustomerController::class, 'edit'])->name('master.customer.edit');
Route::put('/master/customer/update/{id}', [CustomerController::class, 'update'])->name('master.customer.update');
Route::delete('/master/customer/destroy/{id}', [CustomerController::class, 'destroy'])->name('master.customer.destroy');
Route::get('/master/customer/export', [CustomerController::class, 'export'])->name('master.customer.export');
Route::post('/master/customer/import', [CustomerController::class, 'import'])->name('master.customer.import');

// Stock GA
Route::get('/stock_ga', [StockGaController::class, 'index'])->name('stock_ga.index');
Route::post('/stock_ga/store', [StockGaController::class, 'store'])->name('stock_ga.store');
Route::patch('/stock_ga/addStock/{id}', [StockGaController::class, 'addStock'])->name('stock_ga.addStock');
Route::get('/stock_ga/export', [StockGaController::class, 'export'])->name('stock_ga.export');
Route::post('/stock_ga/import', [StockGaController::class, 'import'])->name('stock_ga.import');

// vendor
Route::get('/master/vendor', [VendorController::class, 'index'])->name('master.vendor.index');
Route::get('/master/vendor/create', [VendorController::class, 'create'])->name('master.vendor.create');
Route::post('/master/vendor/store', [VendorController::class, 'store'])->name('master.vendor.store');
Route::get('/master/vendor/show/{id}', [VendorController::class, 'show'])->name('master.vendor.show');
Route::get('/master/vendor/edit/{id}', [VendorController::class, 'edit'])->name('master.vendor.edit');
Route::put('/master/vendor/update/{id}', [VendorController::class, 'update'])->name('master.vendor.update');
Route::delete('/master/vendor/destroy/{id}', [VendorController::class, 'destroy'])->name('master.vendor.destroy');

// Reports
Route::get('/report/jurnal_daily', [ReportJurnalDailyController::class, 'index'])->name('report.jurnal_daily.index');

// Wilayah
Route::get('/master/wilayah', [WilayahController::class, 'index'])->name('master.wilayah.index');
Route::get('/master/wilayah/create', [WilayahController::class, 'create'])->name('master.wilayah.create');
Route::post('/master/wilayah/store', [WilayahController::class, 'store'])->name('master.wilayah.store');
Route::delete('/master/wilayah/destroy/{id}', [WilayahController::class, 'destroy'])->name('master.wilayah.destroy');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});