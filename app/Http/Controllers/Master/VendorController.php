<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Vendors;
use Illuminate\Http\Request;
use App\Models\Master\Provinsi;
use App\Models\Master\Kabupaten;
use App\Models\Master\Kecamatan;
use App\Models\Master\Kelurahan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data['vendors'] = Vendors::get();

        return view('master.vendor.index', $data);
    }

    public function create(Request $request)
    {
        $data['provinsi'] = Provinsi::get();

        return view('master.vendor.create', $data);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
        ]);

        Vendors::create([
            'nama' => $request->name,
            'alamat' => $request->alamat,
            'phone' => $request->phone,
            'owner' => $request->owner,
            'provinsi_id' => $request->provinsi,
            'kabupaten_id' => $request->kota,
            'kecamatan_id' => $request->kecamatan,
            'kelurahan_id' => $request->kelurahan,
            'created_by' => Auth::id(),
            'updated_by' => null,
        ]);

        // Redirect back with a success message
        return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil dibuat!.');
    }

    public function show($id)
    {
        $data['vendors'] = Vendors::findOrFail($id);

        return view('master.vendor.show', $data);
    }

    public function edit($id)
    {
        $data['vendors'] = Vendors::findOrFail($id);
        $data['provinsi'] = Provinsi::get();
        $data['kabupaten'] = Kabupaten::get();
        $data['kelurahan'] = Kelurahan::get();
        $data['kecamatan'] = Kecamatan::get();

        return view('master.vendor.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'provinsi' => 'required',
            'kota' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
        ]);

        $vendor = Vendors::findOrFail($id);

        $vendor->nama = $request->name;
        $vendor->nama = $request->name;
        $vendor->phone = $request->phone;
        $vendor->owner = $request->owner;
        $vendor->provinsi_id = $request->provinsi;
        $vendor->kabupaten_id = $request->kota;
        $vendor->kecamatan_id = $request->kecamatan;
        $vendor->kelurahan_id = $request->kelurahan;
        $vendor->updated_by = Auth::id();

        try {
            DB::beginTransaction();
            
            $vendor->save();
            
            DB::commit();
            return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil diubah!.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors('Failed to update Vendor: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $vendor = Vendors::find($id);

        if ($vendor) {
            $vendor->delete();
            return redirect()->route('master.vendor.index')->with('success', 'Vendor berhasil dihapus!.');
        }

        return redirect()->route('master.customer.index')->with('error', 'Vendor tidak ditemukan!.');
    }

    public function timeShift($id)
    {
        
    }
}
