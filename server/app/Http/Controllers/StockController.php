<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $bloodBank = $request->user();
        $stock = $bloodBank->stocks()->get();
        return response()->json($stock);
    }

    public function store(Request $request)
    {
        $bloodBank = $request->user();
        $validated = $request->validate([
            'groupe_sanguin' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'quantite' => 'required|integer|min:0',
        ]);
        $stock = $bloodBank->stocks()->create($validated);
        return response()->json($stock, 201);
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:0',
        ]);
        $stock->update($validated);
        return response()->json($stock);
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return response()->json(['message' => 'Stock supprimé']);
    }
} 