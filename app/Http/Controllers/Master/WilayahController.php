<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Provinsi;
use App\Models\Master\Kabupaten;
use App\Models\Master\Kecamatan;
use App\Models\Master\Kelurahan;
use App\Models\Master\Wilayah;
use App\Models\User;
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
        $data['users'] = User::get();

        return view('master.wilayah.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'user' => 'required|exists:users,id',
            'provinsi' => 'required|exists:provinces,id', // Validasi provinsi harus ada di tabel 'provinsi'
            'wilayah' => 'required|string|max:255', // Nama kawasan wajib diisi
            'kota' => 'required|array', // Kota wajib berupa array
            'kota.*' => 'exists:regencies,id' // Setiap kota dalam array harus valid
        ]);

        // Gabungkan kota menjadi string dipisahkan koma (atau JSON jika ingin)
        $kabupatenIds = implode(',', $request->kota);

        // Simpan data wilayah
        $wilayah = Wilayah::create([
            'provinsi_id' => $request->provinsi,
            'nama_kawasan' => $request->wilayah,
            'kabupaten_id' => $kabupatenIds, // Simpan sebagai string
            'created_by' => auth()->user()->id, // Ambil ID user yang sedang login
        ]);

        // Update data kabupaten terkait
        Kabupaten::whereIn('id', $request->kota)->update(['area' => $request->wilayah]);

        // update user dipilih
        $user = User::find($request->user);
        $user->area = $request->wilayah;
        $user->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('master.wilayah.index')->with('success', 'Wilayah berhasil disimpan!');
    }

    public function destroy($id)
    {
        $decryptedId = decrypt($id);
        $wilayah = Wilayah::find($decryptedId);

        if ($wilayah) {
            // Remove area from related Kabupaten
            $kabupaten = Kabupaten::where('area', $wilayah->nama_kawasan)->first();
            if ($kabupaten) {
                $kabupaten->area = null;
                $kabupaten->save();
            }

            $wilayah->delete();
            return redirect()->route('master.wilayah.index')->with('success', 'Wilayah Berhasil di hapus.');
        }

        return redirect()->route('master.wilayah.index')->with('error', 'Wilayah tidak ditemukan.');
    }
}
