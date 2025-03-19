<?php

namespace App\Imports;

use App\Models\Master\Customer;
use App\Models\Master\Provinsi;
use App\Models\Master\Kabupaten;
use App\Models\Master\Kelurahan;
use App\Models\Master\Kecamatan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use App\Models\User;
use Auth;
use DB;


class CustomerImport implements ToCollection, WithHeadingRow, WithStartRow, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    public $error = [];
    public $success = [];

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $collect_error = [];
            $collect_success = [];

            foreach ($rows as $row) {
                // Cek apakah kolom yang dibutuhkan ada
                if (!isset($row['spg']) || !isset($row['nama']) || !isset($row['telepon'])) {
                    $collect_error[] = 'Missing required columns in row';
                    continue;
                }

                // Periksa apakah provinsi kosong
                $provinsi = null;
                if (!empty($row['provinsi'])) {
                    $provinsi = Provinsi::whereRaw('LOWER(name) = ?', [strtolower($row['provinsi'])])->first();
                    if (!$provinsi) {
                        $collect_error[] = $row['provinsi'] . 'not found';
                        continue;
                    }
                }

                // cari kabupaten
                $kabupaten = Kabupaten::whereRaw('LOWER(name) = ?', [strtolower($row['kota'])])->first();
                if (!$kabupaten) {
                    $collect_error[] = $row['kota'] . ' not found';
                    continue;
                }

                // Periksa apakah kelurahan kosong
                $kecamatan = null;
                if (!empty($row['kecamatan'])) {
                    $kecamatan = Kecamatan::whereRaw('LOWER(name) = ?', [strtolower($row['kecamatan'])])->first();
                    if (!$kecamatan) {
                        $collect_error[] = $row['kecamatan'] . 'not found';
                        continue;
                    }
                }

                // Periksa apakah kelurahan kosong
                $kelurahan = null;
                if (!empty($row['kelurahan'])) {
                    $kelurahan = Kelurahan::whereRaw('LOWER(name) = ?', [strtolower($row['kelurahan'])])->first();
                    if (!$kelurahan) {
                        $collect_error[] = $row['kelurahan'] . 'not found';
                        continue;
                    }
                }

                // Cari SPG berdasarkan email
                $spg = User::where('email', $row['spg'])->first();
                if (!$spg) {
                    $collect_error[] = $row['spg'] . 'not found';
                    continue;
                }

                $customer = new Customer;
                $customer->user_id = $spg->id;
                $customer->nama = $row['nama'];
                $customer->alamat = $row['alamat'] ?? null;
                $customer->phone = $row['telepon'] ?? null;
                $customer->owner = $row['owner'] ?? null;
                $customer->provinsi_id = $provinsi->id;
                $customer->kabupaten_id = $kabupaten->id ?? null;
                $customer->kecamatan_id = $kecamatan->id ?? null;
                $customer->kelurahan_id = $kelurahan->id ?? null;
                $customer->status = 1;
                $customer->created_by = Auth::id();
                $customer->save();

                $collect_success[] = $row['nama'] . ' berhasil diimport';
            }

            // Set pesan sukses atau error jika tidak ada data
            if (empty($collect_success)) {
                $collect_success[] = 'No successful import.';
            }

            if (empty($collect_error)) {
                $collect_error[] = 'No failed import.';
            }

            $this->error = $collect_error;
            $this->success = $collect_success;

            DB::commit();
        } catch (\Exception $e) {
            $this->error = [$e->getMessage()];
            DB::rollBack();
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}