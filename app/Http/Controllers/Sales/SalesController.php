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

        // Filter data untuk bulan dan tahun saat ini
        $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                ->whereYear('tanggal_order', $currentYear)
                                ->where('status', 2)
                                ->get();

        return view('penjualan.review', $data);
    }

    public function settle(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Filter data untuk bulan dan tahun saat ini
        $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                ->whereYear('tanggal_order', $currentYear)
                                ->where('status', 3)
                                ->get();

        return view('penjualan.settle', $data);
    }

    public function create_senses(Request $request)
    {
        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();

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
                return redirect()->back()->withErrors(['error' => 'You must select a customer or provide cash value.']);
            }

            // Input penjualan index
            $penjualan = new SalesOrder;
            $penjualan->kode = SalesOrder::generateSO();
            $penjualan->customer_id = $customer_id;
            $penjualan->type = $customer_type;
            $penjualan->brand_name = $request->brand_name;
            $penjualan->tanggal_order = date('Y-m-d');
            $penjualan->status = 2;
            $penjualan->created_by = Auth::id();
            $penjualan->save();

            // Input penjualan GA (optional)
            if (!empty($request->variant) && !empty($request->qty)) {
                foreach ($request->variant as $index => $variant) {
                    $stock = StockGa::where('product_id', $variant)->first();

                    if (!$stock || $stock->qty < $request->qty[$index]) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['error' => 'Stock variant Give Away: ' . $variant . ' tidak mencukupi!']);
                    }

                    SalesOrderGa::create([
                        'so_id' => $penjualan->id,
                        'product_packaging_id' => $variant,
                        'qty' => $request->qty[$index],
                    ]);

                    // Reduce stock
                    $stock->qty -= $request->qty[$index];
                    $stock->pcs = $stock->qty / 45;
                    $stock->save();
                }
            }

            // Input penjualan item
            foreach ($request->transaksi as $index => $transaksi) {
                SalesOrderItem::create([
                    'so_id' => $penjualan->id,
                    'product_id' => $transaksi,
                    'qty' => $request->transaksi_qty[$index],
                    'unit_weight' => 1,
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->back()->with('success', 'Data transaksi telah disimpan.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();

            // Redirect with error message
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data transaksi.');
        }
    }

    public function edit($id)
    {
        // Find the sales order by its ID
        $sales = SalesOrder::findOrFail($id);

        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();
        $data['sales'] = $sales;
        $data['sales_items'] = SalesOrderItem::where('so_id', $id)->get();
        $data['sales_ga'] = SalesOrderGa::where('so_id', $id)->get();

        // Pass the data to the view
        return view('penjualan.edit', $data);
    }

    public function update(Request $request, $id)
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
            // Find the sales order by its ID
            $penjualan = SalesOrder::findOrFail($id);

            // // Determine customer type and value
            // $customer_id = $request->customer_dom ?? $request->customer_non_dom ?? null;
            // $customer_type = !empty($request->customerCash) ? 1 : 0;

            // if (!$customer_id && $customer_type === 0) {
            //     return redirect()->back()->withErrors(['error' => 'You must select a customer or provide cash value.']);
            // }

            // Update penjualan index
            $penjualan->customer_id = $request->customer;
            $penjualan->brand_name = $request->brand_name;
            $penjualan->updated_by = Auth::id();
            $penjualan->save();

            // Update penjualan GA (optional)
            // Restore the previous stock before deleting SalesOrderGa
            $default_ml_pcs = 45;
            $previousSalesGa = SalesOrderGa::where('so_id', $penjualan->id)->get();
            foreach ($previousSalesGa as $salesGa) {
                $stock = StockGa::where('product_id', $salesGa->product_packaging_id)->first();
                if ($stock) {
                    $stock->qty += $salesGa->qty;
                    $get_pcs = $stock->qty / $default_ml_pcs;
                    $stock->pcs = $get_pcs;
                    $stock->save();
                }
            }

            // Delete existing SalesOrderGa records
            SalesOrderGa::where('so_id', $penjualan->id)->delete();

            // Add new SalesOrderGa records
            if (!empty($request->variant) && !empty($request->qty)) {
                foreach ($request->variant as $index => $variant) {
                    $stock = StockGa::where('product_id', $variant)->first();

                    if (!$stock || $stock->qty < $request->qty[$index]) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['error' => 'Stock variant Give Away: ' . $variant . ' tidak mencukupi!']);
                    }

                    SalesOrderGa::create([
                        'so_id' => $penjualan->id,
                        'product_packaging_id' => $variant,
                        'qty' => $request->qty[$index],
                    ]);

                    // Reduce stock
                    $stock->qty -= $request->qty[$index];
                    $stock->pcs = $stock->qty / 45;
                    $stock->save();
                }
            }

            // Update Transaksi
            SalesOrderItem::where('so_id', $penjualan->id)->delete();
            foreach ($request->transaksi as $index => $transaksi) {
                SalesOrderItem::create([
                    'so_id' => $penjualan->id,
                    'product_id' => $transaksi,
                    'qty' => $request->transaksi_qty[$index],
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->back()->with('success', 'Data transaksi telah diperbarui.');
        } catch (\Exception $e) {
            dd($e);
            // Rollback the transaction in case of error
            DB::rollback();

            // Redirect with error message
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data transaksi.');
        }
    }

    public function destroy($id)
    {
        try {
            // Find the sales order by its ID
            $sales = SalesOrder::findOrFail($id);

            // Delete the sales order
            $sales->status = 0 ;
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
            return redirect()->route('penjualan.index')->with('success', 'Jurnal berhasil di hapus.');

        } catch (\Exception $e) {
            // Handle the error and redirect back with error message
            return redirect()->route('penjualan.index')->with('error', 'Gagal menghapus Jurnal.');
        }
    }

    public function checkCustomerDOM()
    {
        // Get the authenticated user
        $user = User::find(Auth::id());

        // Fetch customers with related location data
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

        // Return customers as JSON
        return response()->json($customers);
    }

    public function checkCustomerOUTDOM()
    {
        // Get the authenticated user
        $user = User::find(Auth::id());

        // Fetch customers from a different province or regency
        $customers = Customer::leftJoin('provinces', 'provinces.id', '=', 'master_customer.provinsi_id')
            ->leftJoin('regencies', 'regencies.id', '=', 'master_customer.kabupaten_id')
            ->leftJoin('districts', 'districts.id', '=', 'master_customer.kecamatan_id')
            // ->where('master_customer.provinsi_id', '!=', $user->provinsi_id)
            ->Where('master_customer.kabupaten_id', '!=', $user->kabupaten_id)
            ->select(
                'master_customer.id as customer_id',
                'master_customer.nama as customer_nama',
                'districts.name as customer_kecamatan',
                'regencies.name as customer_kota',
                'provinces.name as customer_provinsi'
            )
            ->get();

        // Return customers as JSON
        return response()->json($customers);
    }
}
