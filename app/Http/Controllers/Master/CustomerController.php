<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Customer;
use App\Models\Master\Provinsi;
use App\Models\Master\Kabupaten;
use App\Models\Master\Kecamatan;
use App\Models\Master\Kelurahan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data['customers'] = Customer::get();

        return view('master.customer.index', $data);
    }

    public function create(Request $request)
    {
        $data['provinsi'] = Provinsi::get();

        return view('master.customer.create', $data);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        Customer::create([
            'user_id' => Auth::id(),
            'nama' => $request->name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'provinsi_id' => $request->provinsi,
            'kabupaten_id' => $request->kota,
            'kecamatan_id' => $request->kecamatan,
            'kelurahan_id' => $request->kelurahan,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => null,
        ]);

        // Redirect back with a success message
        return redirect()->route('master.customer.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $decryptedId = decrypt($id);
        $data['customer'] = Customer::findOrFail($decryptedId);

        return view('master.customer.show', $data);
    }

    public function edit($id)
    {
        $decryptedId = decrypt($id);
        $data['customer'] = Customer::findOrFail($decryptedId);
        $data['provinsi'] = Provinsi::get();
        $data['kabupaten'] = Kabupaten::get();
        $data['kelurahan'] = Kelurahan::get();
        $data['kecamatan'] = Kecamatan::get();

        return view('master.customer.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->nama = $request->name;
        $customer->nama = $request->name;
        $customer->phone = $request->phone;
        $customer->owner = $request->owner;
        $customer->provinsi_id = $request->provinsi;
        $customer->kabupaten_id = $request->kota;
        $customer->kecamatan_id = $request->kecamatan;
        $customer->kelurahan_id = $request->kelurahan;
        $customer->updated_by = Auth::id();

        try {
            DB::beginTransaction();
            
            $customer->save();
            
            DB::commit();
            return redirect()->route('master.customer.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Failed to update Customer: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $decryptedId = decrypt($id);
        $customer = Customer::find($decryptedId);

        if ($customer) {
            $customer->delete();
            return redirect()->route('master.customer.index')->with('success', 'Customer deleted successfully.');
        }

        return redirect()->route('master.customer.index')->with('error', 'Customer not found.');
    }
}