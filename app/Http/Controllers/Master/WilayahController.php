<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Provinsi;
use App\Models\Master\Kabupaten;
use App\Models\Master\Kecamatan;
use App\Models\Master\Kelurahan;
use App\Models\Master\Wilayah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['wilayah'] = Wilayah::with('provinsi', 'kabupaten')->get();

        return view('master.wilayah.index', $data);
    }

    public function create(Request $request)
    {
        $data['provinsi'] = Provinsi::get();

        return view('master.wilayah.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'provinsi' => 'required|exists:provinces,id', // Validasi provinsi harus ada di tabel 'provinsi'
            'wilayah' => 'required|string|max:255', // Nama kawasan wajib diisi
            'kota' => 'required|array', // Kota wajib berupa array
            'kota.*' => 'exists:regencies,id' // Setiap kota dalam array harus valid
        ]);

        // Simpan data wilayah
        foreach ($request->kota as $kotaId) {
            Wilayah::create([
                'provinsi_id' => $request->provinsi,
                'nama_kawasan' => $request->wilayah,
                'kota_id' => $kotaId,
                'created_by' => auth()->user()->id, // Ambil ID user yang sedang login
            ]);
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('master.wilayah.index')->with('success', 'Wilayah berhasil disimpan!');
    }

    public function destroy($id)
    {
        $decryptedId = decrypt($id);
        $wilayah = Wilayah::find($decryptedId);

        if ($wilayah) {
            $wilayah->delete();
            return redirect()->route('master.wilayah.index')->with('success', 'Wilayah Berhasil di hapus.');
        }

        return redirect()->route('master.wilayah.index')->with('error', 'Wilayah tidak ditemukan.');
    }
}
