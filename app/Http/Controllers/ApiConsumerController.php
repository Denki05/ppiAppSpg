<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ApiConsumerController extends Controller
{
    public function getItemsProducts()
    {
        $response = Http::get('http://ppiapps.sytes.net:8000/api/products');
        
        if ($response->successful()) {
            $items = $response->json();

            $data = [
                'items' => $items
            ];
            
            return view('product', $data); // Use view to display items
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}