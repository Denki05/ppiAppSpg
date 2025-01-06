<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiConsumerController;
use App\Rules\ProductExists;
use App\Models\Penjualan\SalesOrder;
use App\Models\Penjualan\SalesOrderItem;
use App\Models\Penjualan\SalesOrderGa;
use App\Models\Master\Customer;
use App\Models\Master\StockGa;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['sales'] = SalesOrder::get();

        return view('penjualan.index', $data);
    }

    public function review()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::now()->toDateString();
        $user = Auth::user();

        // Check if the logged-in user has the role 'dev' or 'admin_sales'
        if ($user->role === 'dev' || $user->role === 'admin') {
            // Filter data untuk bulan dan tahun saat ini
            $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                ->whereYear('tanggal_order', $currentYear)
                ->whereDate('tanggal_order', $currentDate)
                ->where('status', 2)
                ->get();
        } else {
            // Filter data berdasarkan user provinsi dan kabupaten
            $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                    ->whereYear('tanggal_order', $currentYear)
                                    ->whereDate('tanggal_order', $currentDate)
                                    ->where('status', 2)
                                    ->where('created_by', $user->id)
                                    ->get();
        }

        return view('penjualan.review', $data);
    }

    public function settle(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $user = Auth::user();

        if ($user->role === 'dev' || $user->role === 'admin') {
            // Filter data for the current month and year without date
            $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                    ->whereYear('tanggal_order', $currentYear)
                                    ->where('status', 3)
                                    ->get();
        } else {
            // Filter data for the current month, year, and date
            $currentDate = Carbon::now()->toDateString();
            $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                    ->whereYear('tanggal_order', $currentYear)
                                    ->where('status', 3)
                                    ->where('created_by', $user->id)
                                    ->get();
        }

        return view('penjualan.settle', $data);
    }

    public function create_senses(Request $request)
    {
        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();
        
        $stockGaItems = StockGa::where('brand_name', 'Senses')->get();
        $data['stock_ga'] = $stockGaItems->map(function ($item) {
            $productData = $item->getProductDataFromApi($item->product_id);
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $productData['name'] ?? null,
                'code' => $productData['code'] ?? null,
            ];
        });

        // Pass the product and unit weight data to the view
        return view('penjualan.create_senses', $data);
    }

    public function create_gcf(Request $request)
    {
        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();
        
        $stockGaItems = StockGa::where('brand_name', 'GCF')->get();
        $data['stock_ga'] = $stockGaItems->map(function ($item) {
            $productData = $item->getProductDataFromApi($item->product_id);
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $productData['name'] ?? null,
                'code' => $productData['code'] ?? null,
            ];
        });

        // Pass the product and unit weight data to the view
        return view('penjualan.create_gcf', $data);
    }

    public function store(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'customer_dom' => 'nullable|exists:master_customer,id',
            'customer_non_dom' => 'nullable|exists:master_customer,id',
            'customerCash' => 'nullable|numeric|min:0',
            'brand_name' => 'required|string',
            'variant' => 'nullable|array',
            'qty' => 'nullable|array',
            'qty.*' => 'nullable|numeric|min:1',
            'transaksi' => 'required|array',
            'transaksi_qty' => 'required|array',
            'transaksi_qty.*' => 'required|numeric|min:1',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Determine customer type and value
            $customer_id = $request->customer_dom ?? $request->customer_non_dom ?? null;
            $customer_type = !empty($request->customerCash) ? 1 : 0;

            if (!$customer_id && $customer_type === 0) {
                return redirect()->back()->withErrors(['error' => 'You must select a customer or provide a cash value.']);
            }

            // Create sales order (penjualan index)
            $penjualan = SalesOrder::create([
                'kode' => SalesOrder::generateSO(),
                'customer_id' => $customer_id,
                'type' => $customer_type,
                'brand_name' => $request->brand_name,
                'tanggal_order' => now()->toDateString(),
                'status' => 2,
                'created_by' => Auth::id(),
            ]);

            // Handle "Give Away" (GA) items if applicable
            if (!empty($request->variant) && !empty($request->qty)) {
                foreach ($request->variant as $index => $variant) {
                    $stock = StockGa::where('product_id', $variant)->first();
                    // dd($variant);

                    // Validate stock availability
                    if (!$stock || $stock->qty < $request->qty[$index]) {
                        DB::rollback();
                        return redirect()->back()->withErrors([
                            'error' => 'Stock for Give Away variant: ' . $variant . ' is insufficient!'
                        ]);
                    }

                    // Create GA record and adjust stock
                    SalesOrderGa::create([
                        'so_id' => $penjualan->id,
                        'product_packaging_id' => $variant,
                        'pcs' => $request->qty[$index],
                    ]);

                    // Calculate stock volume
                    $stockBefore = $stock->qty;
                    $stockGetVolume = $request->qty[$index] * 45;
                    $calculateStock = $stockBefore - $stockGetVolume;
                    $stock->qty = $calculateStock;

                    // Calculate stock pcs
                    $stockBeforePcs = $stock->pcs;
                    $stockGetPcs = $request->qty[$index];
                    $calculatePcs = $stockBeforePcs - $stockGetPcs;
                    $stock->pcs = $calculatePcs;
                    $stock->save();
                }
            }

            // Handle main transaction items
            foreach ($request->transaksi as $index => $transaksi) {
                // dd($transaksi);
                SalesOrderItem::create([
                    'so_id' => $penjualan->id,
                    'product_id' => $transaksi,
                    'qty' => $request->transaksi_qty[$index],
                    'unit_weight' => 1, // Assuming a default weight of 1
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('home')->with('success', 'Jurnal berhasil di input.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();

            // Log the exception for debugging
            Log::error('Transaction Store Error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            // Redirect with error message
            return redirect()->back()->with('error', 'An error occurred while saving the transaction.');
        }
    }

    public function edit($id)
    {
        // Find the sales order by its ID
        $decryptedId = decrypt($id);
        $sales = SalesOrder::findOrFail($decryptedId);
        // dd($decryptedId);

        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();
        $data['sales'] = $sales;
        $data['sales_items'] = SalesOrderItem::where('so_id', $sales->id)->get();
        $data['sales_ga'] = SalesOrderGa::where('so_id', $sales->id)->get();

        $stockGaItems = StockGa::where('brand_name', $sales->brand_name)->get();
        $data['stock_ga'] = $stockGaItems->map(function ($item) {
            $productData = $item->getProductDataFromApi($item->product_id);
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $productData['name'] ?? null,
                'code' => $productData['code'] ?? null,
            ];
        });

        // Pass the data to the view
        return view('penjualan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer' => 'nullable|exists:master_customer,id',
            'brand_name' => 'required|string',
            'variant' => 'nullable|array',
            'qty' => 'nullable|array',
            'qty.*' => 'nullable|numeric|min:1',
            'transaksi' => 'required|array',
            'transaksi_qty' => 'required|array',
            'transaksi_qty.*' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Update SalesOrder
            $penjualan = SalesOrder::findOrFail($id);
            $penjualan->customer_id = $request->customer;
            $penjualan->brand_name = $request->brand_name;
            $penjualan->updated_by = Auth::id();
            $penjualan->save();

            // Restore the previous stock before deleting SalesOrderGa
            $default_ml_pcs = 45;
            $previousSalesGa = SalesOrderGa::where('so_id', $penjualan->id)->get();
            foreach ($previousSalesGa as $salesGa) {
                $stock = StockGa::where('product_id', $salesGa->product_packaging_id)->first();
                if ($stock) {
                    // calculate stock pcs
                    $getStockGaPcs = $salesGa->pcs;
                    $stock->pcs += $getStockGaPcs;

                    // calculate stock volume
                    $getStockGaVolume = $getStockGaPcs * $default_ml_pcs;
                    $stock->qty += $getStockGaVolume;

                    $stock->save();
                }
            }

            SalesOrderGa::where('so_id', $penjualan->id)->delete();

            if ($request->has('variant') && $request->has('qty')) {
                foreach ($request->variant as $index => $variantId) {
                    $stock = StockGa::where('product_id', $variantId)->first();
                    // dd($stock->qty);

                    if (!$stock || $stock->qty < ($request->qty[$index] * $default_ml_pcs)) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['error' => 'Insufficient stock for variant: ' . $variantId]);
                    }

                    SalesOrderGa::create([
                        'so_id' => $penjualan->id,
                        'product_packaging_id' => $variantId,
                        'pcs' => $request->qty[$index],
                    ]);

                    // Calculate stock volume
                    $stockBefore = $stock->qty;
                    $stockGetVolume = $request->qty[$index] * 45;
                    $calculateStock = $stockBefore - $stockGetVolume;
                    $stock->qty = $calculateStock;

                    // dd($calculateStock);

                    // Calculate stock pcs
                    $stockBeforePcs = $stock->pcs;
                    $stockGetPcs = $request->qty[$index];
                    $calculatePcs = $stockBeforePcs - $stockGetPcs;
                    $stock->pcs = $calculatePcs;

                    // dd($calculatePcs);
                    $stock->save();
                }
            }

            // Update SalesOrderItem
            SalesOrderItem::where('so_id', $penjualan->id)->delete();

            foreach ($request->transaksi as $index => $productId) {
                SalesOrderItem::create([
                    'so_id' => $penjualan->id,
                    'product_id' => $productId,
                    'qty' => $request->transaksi_qty[$index],
                    'unit_weight' => 1,
                ]);
            }

            DB::commit();
            // return redirect()->back()->with('success', 'Data updated successfully.');
            return redirect()->route('home')->with('success', 'Jurnal berhasil di input.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating SalesOrder', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while updating the data.');
        }
    }

    public function settel($id)
    {
        try{
            // Find the sales order by its ID
            $sales = SalesOrder::findOrFail($id);

            // Update the sales order status to settled
            $sales->status = 3;
            $sales->settel_by = 0; // Assuming 0 is for USER
            $sales->updated_by = Auth::user()->id;
            $sales->save();

            // Redirect back with success message
            return redirect()->route('penjualan.settle')->with('success', 'Jurnal berhasil di setel.');

        } catch (\Exception $e) {
            // Handle the error and redirect back with error message
            return redirect()->route('penjualan.settle')->with('error', 'Gagal menyetel Jurnal.');
        }
    }

    public function destroy($id)
    {
        try {
            // Find the sales order by its ID
            $sales = SalesOrder::findOrFail($id);

            // Restore the stock before deleting SalesOrderGa
            $salesGaItems = SalesOrderGa::where('so_id', $sales->id)->get();
            foreach ($salesGaItems as $salesGa) {
                $stock = StockGa::where('product_id', $salesGa->product_packaging_id)->first();
                if ($stock) {
                    $stock->qty += $salesGa->qty;
                    $stock->pcs = $stock->qty / 45;
                    $stock->save();
                }
            }

            // Soft delete the sales order
            $sales->status = 0;
            $sales->updated_by = Auth::user()->id;
            $sales->deleted_at = now();
            $sales->save();

            // Soft delete related sales GA records
            $sales_ga = SalesOrderGa::where('so_id', $sales->id);
            $sales_ga->update(['deleted_at' => now()]);

            // Soft delete related sales items
            $sales_item = SalesOrderItem::where('so_id', $sales->id);
            $sales_item->update(['deleted_at' => now()]);

            // Redirect back with success message
            // return redirect()->route('penjualan.index')->with('success', 'Jurnal berhasil di hapus.');
            return redirect()->route('home')->with('success', 'Jurnal berhasil di hapus.');

        } catch (\Exception $e) {
            // Handle the error and redirect back with error message
            return redirect()->route('penjualan.index')->with('error', 'Gagal menghapus Jurnal.');
        }
    }

    public function checkCustomerDOM()
    {
        $user = User::find(Auth::id());

        $customers = Customer::leftJoin('provinces', 'provinces.id', '=', 'master_customer.provinsi_id')
            ->leftJoin('regencies', 'regencies.id', '=', 'master_customer.kabupaten_id')
            ->leftJoin('districts', 'districts.id', '=', 'master_customer.kecamatan_id')
            ->where('master_customer.provinsi_id', $user->provinsi_id)
            ->where('master_customer.kabupaten_id', $user->kabupaten_id)
            ->select(
                'master_customer.id as customer_id',
                'master_customer.nama as customer_nama', 
                'districts.name as customer_kecamatan',
                'regencies.name as customer_kota', 
                'provinces.name as customer_provinsi'
            )
            ->get();

        $cityPlaceholder = $customers->isNotEmpty() ? $user->kabupaten->name : 'Domisili';

        return response()->json([
            'customers' => $customers,
            'placeholder' => "PILIH $cityPlaceholder"
        ]);
    }

    public function checkCustomerOUTDOM()
    {
        $user = User::find(Auth::id());

        $customers = Customer::leftJoin('provinces', 'provinces.id', '=', 'master_customer.provinsi_id')
            ->leftJoin('regencies', 'regencies.id', '=', 'master_customer.kabupaten_id')
            ->leftJoin('districts', 'districts.id', '=', 'master_customer.kecamatan_id')
            ->where('master_customer.kabupaten_id', '!=', $user->kabupaten_id)
            ->select(
                'master_customer.id as customer_id',
                'master_customer.nama as customer_nama',
                'districts.name as customer_kecamatan',
                'regencies.name as customer_kota',
                'provinces.name as customer_provinsi'
            )
            ->get();

        $cityPlaceholder = $customers->isNotEmpty() ? "LUAR {$user->kabupaten->name}" : 'Luar Domisili';

        return response()->json([
            'customers' => $customers,
            'placeholder' => "PILIH $cityPlaceholder"
        ]);
    }


    // public function cek_jurnal()
    // {
    //     $tanggalKemarin = now()->subDay()->startOfDay()->toDateString(); // Mendapatkan tanggal kemarin

    //     // Ambil satu jurnal yang masih "review" dan belum ada penanganan sampai kemarin
    //     $jurnal = SalesOrder::where('status', '2')
    //                      ->whereDate('tanggal_order', $tanggalKemarin)
    //                      ->first();

    //     // dd($jurnal);
    // }
}
