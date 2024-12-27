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
            'botol' => 'required|numeric|min:1',
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

        // calculated pcs if input qty
        $default_ml_pcs = 45;
        $get_volume = $request->botol * $default_ml_pcs;

        // dd($get_volume);

        // Save the data to the database
        $stock = new StockGa();
        $stock->product_id = $request->variant;
        $stock->brand_name = $request->brand;
        $stock->qty = $get_volume;
        $stock->pcs = $request->botol;
        $stock->created_by = Auth::id();
        $stock->save();

        // Respond with the success message and redirect URL
        return response()->json([
            'success' => true,
            'message' => 'Stock Variant GA berhasil di input!.',
            'redirect' => route('stock_ga.index'), // Provide the redirect URL
        ]);
    }

    public function addStock(Request $request, $id)
    {
        // Check if the stock entry exists
        $stock = StockGa::find($id);

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock entry not found.',
            ], 404);
        }

        // Validate the request
        $request->validate([
            'additional_stock' => 'required|numeric|min:1',
        ]);

        // Add the additional stock
        $stock->pcs += $request->additional_stock;

        // Calculate total pieces and cartons
        $totalPieces = $stock->pcs;
        $cartons = intdiv($totalPieces, 45); // Calculate full cartons
        $remainingPieces = $totalPieces * 45; // Calculate leftover pieces
        // dd($remainingPieces);

        // Update the stock record
        $stock->qty = $remainingPieces; // Store the remaining pieces
        $stock->save();

        return response()->json([
            'success' => true,
            'message' => "Stock berhasil di tambahkan!.",
        ]);
    }
}
