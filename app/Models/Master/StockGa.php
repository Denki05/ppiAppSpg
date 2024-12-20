<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockGa extends Model
{
    use HasFactory;

    protected $table = "stock_ga";
    protected $fillable =[
        'product_id', 
        'brand_name', 
        'qty',
        'pcs',
        'created_by',
        'updated_by',
    ];
}
