<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PropositionController extends Controller
{
    public function store(Request $request)
    {
        try {

            $request->validate([
                'demande_id' => 'required|exists:demandes,demande_id',
                'date' => 'required|date_format:Y-m-d\TH:i|after:now',
                'message' => 'nullable|string|max:500',
            ]);

            $proposition = Proposition::create([
                'demande_id' => $request->demande_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'message' => $request->message,
                // 'status' => 'en_attente'
            ]);


            return response()->json([
                'message' => 'Proposition created successfully',
                'proposition' => $proposition
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating proposition:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to create proposition',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 