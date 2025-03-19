<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockGaTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['', '', '', ''],
        ];
    }

    public function headings(): array
    {
        return ['spg', 'brand' ,'variant', 'botol_pcs'];
    }
}