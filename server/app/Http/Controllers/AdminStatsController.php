<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Stock;
use App\Models\BloodBank;
use App\Models\IntentionDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    public function getRequestStats()
    {
        // Get requests grouped by date and status
        $requests = Demande::select(
            DB::raw('DATE(created_at) as date'),
            'status',
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('date', 'status')
        ->orderBy('date')
        ->get();

        // Format the data for the chart
        $formattedData = [];
        foreach ($requests as $request) {
            $formattedData[] = [
                'created_at' => $request->date,
                'status' => $request->status,
                'count' => $request->count
            ];
        }

        return response()->json($formattedData);
    }

    public function getStockStats()
    {
        // Get stock data from all centers
        $stock = Stock::select(
            'stocks.groupe_sanguin',
            'stocks.quantite',
            'users.name as center_name'
        )
        ->join('users', 'stocks.centre_id', '=', 'users.id')
        ->get();

        // Format the data for the chart
        $formattedData = [];
        foreach ($stock as $item) {
            $formattedData[] = [
                'groupe_sanguin' => $item->groupe_sanguin,
                'quantite' => $item->quantite,
                'center_name' => $item->center_name
            ];
        }

        return response()->json($formattedData);
    }

    public function getIntentionDonStats()
    {
        // Get donation intentions grouped by date
        $intentions = IntentionDon::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($intentions);
    }

    public function getCenterPerformanceStats()
    {
        // Get number of donations per center
        $performance = Demande::select(
            'users.name as center_name',
            DB::raw('COUNT(*) as total_donations'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_donations')
        )
        ->join('users', 'demandes.centre_id', '=', 'users.id')
        ->where('users.role', 'blood_bank')
        ->groupBy('users.name')
        ->get();

        return response()->json($performance);
    }

    public function getBloodGroupDistributionStats()
    {
        // Get blood group distribution across all centers
        $distribution = Stock::select(
            'groupe_sanguin',
            DB::raw('SUM(quantite) as total_quantity')
        )
        ->groupBy('groupe_sanguin')
        ->get();

        return response()->json($distribution);
    }
} 