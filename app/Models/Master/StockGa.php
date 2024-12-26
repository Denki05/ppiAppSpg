<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class StockGa extends Model
{
    use HasFactory;

    protected $table = "stock_ga";
    protected $fillable = [
        'product_id', 
        'brand_name', 
        'qty',
        'pcs',
        'created_by',
        'updated_by',
    ];

    public function getProductDataFromApi($productId)
    {
        $response = Http::get("http://ppiapps.sytes.net:8000/api/products");

        if ($response->successful()) {
            $products = $response->json();

            // Filter product by ID
            $filteredProducts = array_filter($products, function($product) use ($productId) {
                return $product['id'] === $productId;
            });
            return $filteredProducts ? reset($filteredProducts) : null;
        }

        return null;
    }

}
