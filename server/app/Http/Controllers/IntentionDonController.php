<?php

namespace App\Http\Controllers;

use App\Models\IntentionDon;
use App\Models\User;
use Illuminate\Http\Request;

class IntentionDonController extends Controller
{
    public function index()
    {
        return response()->json(IntentionDon::with('user')->latest()->get());
    }

    public function userIntentions($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json($user->intentionDons()->latest()->get());
    }
} 