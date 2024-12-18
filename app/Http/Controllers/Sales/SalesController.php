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

    public function create(Request $request)
    {
        // Fetch product data from ApiConsumerController
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['brands'] = $apiConsumer->getItemsBrands();
        $data['customer'] = Customer::get();

        // Pass the product and unit weight data to the view
        return view('penjualan.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi data dari form
        $validated = $request->validate([
            // 'customer_dom' => 'required|exists:customers,id',
            'variant' => 'required|array', // Variants harus berupa array
            'qty' => 'required|array', // Qty harus berupa array
            'qty.*' => 'required|numeric|min:1', // Setiap qty harus berupa angka dan lebih besar dari 0
        ]);

        // Mengambil data dari form
        $customer_dom = $request->input('customer_dom'); // Ambil customer_dom
        $customer_non_dom = $request->input('customer_non_dom'); // Ambil customer_non_dom
        $variants = $request->input('variant'); // Ambil array variant[]
        $qtys = $request->input('qty'); // Ambil array qty[]

        dd($customer_non_dom);

        // Looping untuk menyimpan setiap transaksi produk
        // foreach ($variants as $index => $variant) {
        //     $qty = $qtys[$index]; // Ambil qty yang sesuai dengan variant
        //     // Simpan data transaksi ke database
        //     ProductTransaction::create([
        //         'customer_id' => $customer_dom, // Misalnya, kita simpan customer_id
        //         'variant_id' => $variant, // ID variant yang dipilih
        //         'qty' => $qty, // Qty yang dipilih
        //     ]);
        // }

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Data transaksi telah disimpan');
    }

    public function destroy($id)
    {
        try {
            // Find the sales order by its ID
            $sales = SalesOrder::findOrFail($id);

            // Delete the sales order
            $sales->status = 0 ;
            $sales->updated_by = Auth::user()->id;
            $sales->save();

            // Redirect back with success message
            return redirect()->route('penjualan.index')->with('success', 'Sales order deleted successfully.');

        } catch (\Exception $e) {
            // Handle the error and redirect back with error message
            return redirect()->route('penjualan.index')->with('error', 'Failed to delete sales order.');
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
