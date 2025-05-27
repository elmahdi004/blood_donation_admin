<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IntentionDonController;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/blood-banks/register', [BloodBankController::class, 'register']);
Route::post('/blood-banks/login', [BloodBankController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User info route
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes that require specific roles
    Route::middleware('role:admin,blood_bank')->group(function () {
        // Blood Bank Management Routes
        Route::apiResource('blood-banks', BloodBankController::class)->except(['store']);
        Route::apiResource('demandes', DemandeController::class);
        Route::patch('/blood-banks/{bloodBank}/status', [BloodBankController::class, 'updateStatus']);
        Route::apiResource('users', UserController::class)->except(['store', 'create']);
        Route::get('/users/{user}/demandes', [DemandeController::class, 'userDemandes']);
    });

    // Intention Don routes
    Route::middleware(['auth:sanctum', 'role:admin'])->get('/intention-dons', [IntentionDonController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/users/{user}/intention-dons', [IntentionDonController::class, 'userIntentions']);
}); 
