<?php

namespace App\Models\Penjualan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesOrderGa extends Model
{
    protected $table = "penjualan_ga";
    protected $fillable =[
        'so_id', 
        'product_packaging_id', 
        'qty',
    ];

    public function so(){
        return $this->BelongsTo('App\Models\Penjualan\SalesOrder','so_id','id');
    }
}
