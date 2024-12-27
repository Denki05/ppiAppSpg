<?php

namespace App\Models\Penjualan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SalesOrderGa extends Model
{
    protected $table = "penjualan_ga";
    protected $fillable =[
        'so_id', 
        'product_packaging_id', 
        'pcs',
    ];

    public function so(){
        return $this->BelongsTo('App\Models\Penjualan\SalesOrder','so_id','id');
    }

    public function getProductDataFromApi($productId)
    {
        // Fetch all products from the API
        $response = Http::get("http://ppiapps.sytes.net:8000/api/products");

        if ($response->successful()) {
            $products = $response->json();

            // Filter the product by its ID
            foreach ($products as $product) {
                if (isset($product['id']) && $product['id'] == $productId) {
                    return $product; // Return the matching product
                }
            }
        }

        // Return null if product not found or API fails
        return null;
    }
}
