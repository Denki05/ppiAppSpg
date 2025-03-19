<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ApiConsumerController extends Controller
{
    private $apiBaseUrl;

    public function __construct()
    {
        // Menggunakan konfigurasi dari file config/services.php
        $this->apiBaseUrl = config('services.api.base_url', 'https://lssoft88.xyz/api');
    }

    public function getItemsProducts()
    {
        try {
            return Cache::remember('products', now()->addMinutes(10), function () {
                $response = Http::get("{$this->apiBaseUrl}/products");

                if ($response->successful()) {
                    return $response->json();
                }

                return ['error' => 'Failed to fetch data', 'details' => $response->body()];
            });
        } catch (\Exception $e) {
            Log::error('API Request Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Service unavailable'], 503);
        }
    }

    public function getItemsBrands()
    {
        try {
            return Cache::remember('brands', now()->addMinutes(10), function () {
                $response = Http::get("{$this->apiBaseUrl}/brands");

                if ($response->successful()) {
                    return $response->json();
                }

                return ['error' => 'Failed to fetch brands', 'details' => $response->body()];
            });
        } catch (\Exception $e) {
            Log::error('API Request Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Service unavailable'], 503);
        }
    }

    public function getProductsByBrand($brand)
    {
        try {
            if (!$brand) {
                return response()->json(['error' => 'Brand parameter is required'], 400);
            }

            return Cache::remember("products_by_brand_{$brand}", now()->addMinutes(10), function () use ($brand) {
                $response = Http::get("{$this->apiBaseUrl}/products");

                if ($response->successful()) {
                    $products = collect($response->json());

                    // Filter produk berdasarkan brand menggunakan Collection
                    $filteredProducts = $products->filter(function ($product) use ($brand) {
                        return isset($product['brand_name']) && strtolower($product['brand_name']) === strtolower($brand);
                    })->values();

                    return $filteredProducts;
                }

                return ['error' => 'Failed to fetch products', 'details' => $response->body()];
            });
        } catch (\Exception $e) {
            Log::error('API Request Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Service unavailable'], 503);
        }
    }
}