<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiConsumerController;
use App\Models\Master\StockGa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StockGaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $apiConsumer = new ApiConsumerController();
        $data['products'] = $apiConsumer->getItemsProducts();
        $data['stocks'] = StockGa::get();

        return view('master.stock_ga.index', $data);
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'variant' => 'required',
            'brand' => 'required|string',
            'qty' => 'required|numeric|min:1',
        ]);

        // Check if the combination of product_id and brand already exists
        $existingEntry = StockGa::where('product_id', $request->variant)
                            ->where('brand_name', $request->brand)
                            ->first();

        if ($existingEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Variant Sudah ada!',
            ], 400);
        }

        // Save the data to the database
        $stock = new StockGa();
        $stock->product_id = $request->variant;
        $stock->brand_name = $request->brand;
        $stock->qty = $request->qty;
        $stock->created_by = Auth::id();
        $stock->save();

        // Respond with the success message and redirect URL
        return response()->json([
            'success' => true,
            'message' => 'Stock Variant GA berhasil di input!.',
            'redirect' => route('stock_ga.index'), // Provide the redirect URL
        ]);
    }

    
}
