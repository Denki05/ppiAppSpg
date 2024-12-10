<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiConsumerController;
use App\Models\Penjualan\SalesOrder;
use App\Models\Penjualan\SalesOrderItem;
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
        try {
            DB::beginTransaction(); // Start a transaction

            // Validate the request data
            $validated = $request->validate([
                'tanggal_order' => 'required|date',
                'brand' => 'required|string|max:50',
                'nama_customer' => 'required|string|max:255',
                'alamat_customer' => 'required|string|max:255',
                'kontak_person' => 'required|string|max:255',
                'telpon' => 'nullable|string|max:20',
                'products' => 'required|array',
                'products.*.variant' => 'required', // No database check because it’s from API
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.unit_weight' => 'required|in:1,2' // Assuming 1 => 'ML', 2 => 'KG'
            ]);

            // Generate a unique sales order code
            $salesOrderCode = SalesOrder::generateSO();

            // Create the sales order
            $salesOrder = SalesOrder::create([
                'kode' => $salesOrderCode,
                'tanggal_order' => $validated['tanggal_order'],
                'brand' => $validated['brand'],
                'nama_customer' => $validated['nama_customer'],
                'alamat_customer' => $validated['alamat_customer'],
                'kontak_person' => $validated['kontak_person'],
                'telpon' => $validated['telpon'],
                'status' => 1,
                'created_by' => Auth::user()->id,
            ]);

            // Loop through the products array and create order items
            foreach ($validated['products'] as $product) {

                SalesOrderItem::create([
                    'so_id' => $salesOrder->id,
                    'product_id' => $product['variant'],
                    'qty' => $product['quantity'],
                    'unit_weight' => $product['unit_weight']
                ]);
            }

            DB::commit(); // Commit the transaction

            if(Auth::user()->role == "spg") {
                return redirect()->route('penjualan.create')->with('success', 'Sale created successfully.');
            } else {
                return redirect()->route('penjualan.index')->with('success', 'Sale created successfully.');
            }

            
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack(); // Rollback on error
            Log::error('Error creating sale: ' . $e->getMessage());

            return redirect()->route('penjualan.create')
                ->withErrors(['error' => 'Failed to create sale. Please try again later.'])
                ->withInput();
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
