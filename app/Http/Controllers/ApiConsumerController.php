<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ApiConsumerController extends Controller
{
    public function getItemsProducts()
    {
        // Fetch product data from API
        $response = Http::get('http://ppiapps.sytes.net:8000/api/products');
        
        if ($response->successful()) {
            $items = $response->json();
            return $items; // Return data instead of view
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

    public function getItemsBrands()
    {
        // Fetch product data from API
        $response = Http::get('http://ppiapps.sytes.net:8000/api/brands');
        
        if ($response->successful()) {
            $items = $response->json();
            return $items; // Return data instead of view
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

    public function getProductsByBrand($brandName)
    {
        // Fetch all products
        $response = Http::get("http://ppiapps.sytes.net:8000/api/products");

        if ($response->successful()) {
            $products = $response->json();

            // Filter products based on the selected brand name
            $filteredProducts = array_filter($products, function ($product) use ($brandName) {
                return isset($product['brand_name']) && $product['brand_name'] === $brandName;
            });

            return response()->json(array_values($filteredProducts));
        }

        return response()->json(['error' => 'Failed to fetch products'], 500);
    }
}