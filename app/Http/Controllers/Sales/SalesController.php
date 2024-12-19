<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiConsumerController;
use App\Rules\ProductExists;
use App\Models\Penjualan\SalesOrder;
use App\Models\Penjualan\SalesOrderItem;
use App\Models\Penjualan\SalesOrderGa;
use App\Models\Master\Customer;
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
            'variant' => 'required|array',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:1',
            'transaksi' => 'required|array',
            'transaksi_qty' => 'required|array',
            'transaksi_qty.*' => 'required|numeric|min:1',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Determine customer type and value
            $customer_id = null;
            $customer_type = 0;

            if (!empty($request->customer_dom) || !empty($request->customer_non_dom)) {
                $customer_id = $request->customer_dom ?? $request->customer_non_dom;
            } elseif (!empty($request->customerCash)) {
                $customer_id = $request->customerCash;
                $customer_type = 1;
            } else {
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

            // Input penjualan GA
            foreach ($request->variant as $index => $variant) {
                SalesOrderGa::create([
                    'so_id' => $penjualan->id,
                    'product_packaging_id' => $variant,
                    'qty' => $request->qty[$index],
                ]);
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

            // Log the error (optional)
            \Log::error('Failed to store transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Redirect with error message
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
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

    public function review()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Filter data untuk bulan dan tahun saat ini
        $data['sales'] = SalesOrder::whereMonth('tanggal_order', $currentMonth)
                                ->whereYear('tanggal_order', $currentYear)
                                ->get();

        return view('penjualan.index', $data);
    }
}
