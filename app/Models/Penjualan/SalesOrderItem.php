<?php

namespace App\Models\Penjualan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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

    public function getProductDataFromApi($productId)
    {
        // Fetch all products from the API
        $response = Http::get("https://lssoft88.xyz/api/products");

        if ($response->successful()) {
            $products = $response->json();

            // Filter products based on the product ID
            $filteredProducts = array_filter($products, function ($product) use ($productId) {
                return isset($product['id']) && $product['id'] === $productId;
            });

            // Return the first matching product or null if not found
            return !empty($filteredProducts) ? reset($filteredProducts) : null;
        }

        // Return null if the API call fails
        return null;
    }
}
