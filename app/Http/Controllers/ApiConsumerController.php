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

    public function getProductsByBrand($brand)
    {
        // Fetch all products
        $response = Http::get("http://ppiapps.sytes.net:8000/api/products");

        if ($response->successful()) {
            $products = $response->json();

            // Filter products based on the selected brand name
            $filteredProducts = array_filter($products, function ($product) use ($brand) {
                return isset($product['brand_name']) && $product['brand_name'] === $brand;
            });

            return response()->json(array_values($filteredProducts));
        }

        return response()->json(['error' => 'Failed to fetch products'], 500);
    }

    public function getProvince()
    {
        // Fetch product data from API
        $response = Http::get('https://api.binderbyte.com/wilayah/provinsi?api_key=5da8d4ef83a1ecdeebda9e566020624823e2f528c8fa3ab56a7db84025e9dd2c');
        
        if ($response->successful()) {
            $items = $response->json();
            // Ensure the 'provinsi' key exists in the response and return its contents
            return $items['value'] ?? []; // 'value' is the key where actual provinces may be stored; update it based on your API's response structure.
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}