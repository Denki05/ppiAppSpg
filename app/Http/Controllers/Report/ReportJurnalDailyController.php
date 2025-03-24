<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiConsumerController;
use Illuminate\Http\Request;
use App\Models\Penjualan\SalesOrder;
use App\Models\Master\Customer;
use App\Models\User;
use DB;

class ReportJurnalDailyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {   
        $get_data = SalesOrder::whereIn('penjualan_so.status', [1, 2, 3])
            ->leftJoin('penjualan_so_item', 'penjualan_so.id', '=', 'penjualan_so_item.so_id')
            ->leftJoin('penjualan_ga', 'penjualan_so.id', '=', 'penjualan_ga.so_id')
            ->leftJoin('master_customer', 'penjualan_so.customer_id', '=', 'master_customer.id')
            ->leftJoin('users', 'penjualan_so.created_by', '=', 'users.id')
            ->select(
                'penjualan_so.created_at AS tanggal_jurnal',
                'penjualan_so.kode AS kode_jurnal',
                'penjualan_so.brand_name AS brand_jurnal',
                DB::raw("
                    CASE 
                        WHEN penjualan_so.customer_id IS NULL THEN 'CASH'
                        ELSE master_customer.nama
                    END AS customer
                "),
                DB::raw("
                    CASE
                        WHEN penjualan_so.status = 1 THEN 'AWAL'
                        WHEN penjualan_so.status = 2 THEN 'REVIEW'
                        WHEN penjualan_so.status = 3 THEN 'SETTEL'
                    END AS status_jurnal
                "),
                DB::raw("COALESCE(SUM(penjualan_so_item.qty), 0) AS total_qty"),
                DB::raw("COALESCE(SUM(penjualan_ga.pcs), 0) AS total_qty_ga"),
                DB::raw("
                    CASE 
                        WHEN penjualan_so.created_by IN (3, 8, 9, 10, 11, 12, 13, 14) 
                        THEN users.name 
                        ELSE 'UNKNOWN'
                    END AS spg
                ")
            )
            ->groupBy('penjualan_so.id', 'penjualan_so.created_at', 'penjualan_so.kode', 'penjualan_so.brand_name', 'penjualan_so.customer_id', 'master_customer.nama', 'penjualan_so.status', 'penjualan_so.created_by', 'users.name')
            ->get();

        $sales = User::where('role', 'spg')->get();

        $data = [
            'get_data' => $get_data,
            'sales' => $sales,
        ];

        return view('report.jurnal_daily.index', $data);
    }   
}