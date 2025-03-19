<?php

namespace App\Imports;

use App\Models\Master\StockGa;
use App\Http\Controllers\ApiConsumerController;
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
use DB;
use App\Models\User;
use Auth;
use Carbon\Carbon;

class StockGaImport implements ToCollection, WithHeadingRow, WithStartRow, SkipsOnFailure, SkipsOnError
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
                if (!isset($row['spg']) || !isset($row['brand']) || !isset($row['variant']) || !isset($row['botol_pcs'])) {
                    $collect_error[] = 'Missing required columns in row';
                    continue;
                }

                // Inisialisasi API Consumer
                $apiConsumer = new ApiConsumerController();
                $get_products = $apiConsumer->getItemsProducts();
                $get_brands = $apiConsumer->getItemsBrands();

                // Cari SPG berdasarkan email
                $spg = User::where('email', $row['spg'])->first();
                if (!$spg) {
                    $collect_error[] = $row['spg'] . ' "SPG" not found';
                    continue;
                }

                // Cari Brand
                $brand = collect($get_brands)->first(function ($item) use ($row) {
                    return isset($item['brand_name']) && strtolower(trim($item['brand_name'])) == strtolower(trim($row['brand']));
                });

                if (!$brand || !isset($brand['brand_name'])) {
                    $collect_error[] = $row['brand'] . ' "BRAND" not found';
                    continue;
                }

                // Cari Product
                $product = collect($get_products)->first(function ($item) use ($row) {
                    return isset($item['name']) && strcasecmp(trim($item['name']), trim($row['variant'])) == 0;
                });

                if (!$product || !isset($product['id'])) {
                    $collect_error[] = $row['variant'] . ' "VARIANT" not found';
                    continue;
                }

                // Cek apakah data sudah ada
                $existingEntry = StockGa::where('product_id', $product['id'])
                    ->where('brand_name', $brand['brand_name'])
                    ->where('user_id', Auth::id())
                    ->exists();

                if ($existingEntry) {
                    $collect_error[] = $row['variant'] . ' sudah ada dalam database';
                    continue;
                }

                // Hitung pcs
                $default_ml_pcs = 45;
                $get_volume = ((int) $row['botol_pcs']) * $default_ml_pcs;

                // Simpan ke database
                StockGa::create([
                    'product_id' => $product['id'],
                    'user_id' => $spg->id,
                    'brand_name' => $brand['brand_name'],
                    'qty' => $get_volume,
                    'pcs' => $row['botol_pcs'] ?? 0,
                    'created_by' => Auth::id(),
                ]);

                $collect_success[] = $row['variant'] . ' berhasil diimport';
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