<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['', '', '', '', ''],
        ];
    }

    public function headings(): array
    {
        return ['spg' ,'nama', 'alamat', 'telepon', 'owner', 'provinsi', 'kota', 'kecamatan', 'keluruahan'];
    }
}