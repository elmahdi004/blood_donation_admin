<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodBankController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('demandes', DemandeController::class);

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Blood Bank Authentication Routes
Route::post('/blood-banks/register', [BloodBankController::class, 'register']);
Route::post('/blood-banks/login', [BloodBankController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Blood Bank Management Routes
    Route::apiResource('blood-banks', BloodBankController::class)->except(['store']);
    Route::patch('/blood-banks/{bloodBank}/status', [BloodBankController::class, 'updateStatus']);
}); 
