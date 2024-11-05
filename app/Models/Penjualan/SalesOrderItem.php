<?php

namespace App\Models\Penjualan;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $table = "penjualan_so_item";
    protected $fillable =[
        'so_id', 
        'product_id', 
        'qty', 
        'unit_weight', 
    ];

    const UNIT_WEIGHT = [
    	1 => 'ML',
    	2 => 'KG'
    ];

    public function so(){
        return $this->BelongsTo('App\Models\Penjualan\SalesOrder','so_id','id');
    }
}
