<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IntentionDonController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\AvailableSlotController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\PropositionController;


// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
Route::post('/blood-banks/register', [BloodBankController::class, 'register']);
Route::post('/blood-banks/login', [BloodBankController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Public authenticated routes
    Route::get('/blood-banks', [BloodBankController::class, 'index']);
    Route::get('/blood-banks/{bloodBank}', [BloodBankController::class, 'show']);

    // User info route
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Blood requests routes
    Route::get('/blood-requests', [BloodRequestController::class, 'index']);
    Route::post('/blood-requests', [BloodRequestController::class, 'store']);

    // Donors routes
    Route::get('/donors', [DonorController::class, 'index']);
    Route::get('/blood-banks/{bloodBank}/slots', [AvailableSlotController::class, 'getAvailableSlots']);

    Route::post('/rendez-vous', [RendezVousController::class, 'store']);

    Route::get('/demandes/user', [DemandeController::class, 'getUserRequests']);

    
    // Routes that require specific roles
    Route::middleware('role:admin,blood_bank')->group(function () {
        // Blood Bank Management Routes
        Route::apiResource('blood-banks', BloodBankController::class)->except(['index', 'show', 'store']);
        Route::apiResource('demandes', DemandeController::class);
        Route::patch('/blood-banks/{bloodBank}/status', [BloodBankController::class, 'updateStatus']);
        Route::apiResource('users', UserController::class)->except(['store', 'create']);
        Route::get('/users/{user}/demandes', [DemandeController::class, 'userDemandes']);

        // Admin Statistics Routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/requests/stats', [AdminStatsController::class, 'getRequestStats']);
            Route::get('/admin/stock/stats', [AdminStatsController::class, 'getStockStats']);
            Route::get('/admin/intentions/stats', [AdminStatsController::class, 'getIntentionDonStats']);
            Route::get('/admin/performance/stats', [AdminStatsController::class, 'getCenterPerformanceStats']);
            Route::get('/admin/blood-group-distribution/stats', [AdminStatsController::class, 'getBloodGroupDistributionStats']);
        });
    });

    // Intention Don routes
    Route::middleware(['auth:sanctum', 'role:admin'])->get('/intention-dons', [IntentionDonController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/users/{user}/intention-dons', [IntentionDonController::class, 'userIntentions']);

    // Stock management routes
    Route::middleware(['auth:sanctum', 'role:blood_bank'])->group(function () {
        Route::get('/stock', [StockController::class, 'index']);
        Route::post('/stock', [StockController::class, 'store']);
        Route::put('/stock/{stock}', [StockController::class, 'update']);
        Route::delete('/stock/{stock}', [StockController::class, 'destroy']);

        // Rendez-vous management
        Route::get('/rendez-vous', [RendezVousController::class, 'index']);
        Route::put('/rendez-vous/{rendezVous}', [RendezVousController::class, 'update']);
        Route::delete('/rendez-vous/{rendezVous}', [RendezVousController::class, 'destroy']);

        // Available slots management
        Route::get('/slots', [AvailableSlotController::class, 'index']);
        Route::post('/slots', [AvailableSlotController::class, 'store']);
        Route::put('/slots/{slot}', [AvailableSlotController::class, 'update']);
        Route::delete('/slots/{slot}', [AvailableSlotController::class, 'destroy']);
    });

    // Proposition routes
    Route::post('/propositions', [PropositionController::class, 'store']);

    // Blood Bank Routes
    Route::get('/blood-banks', [BloodBankController::class, 'index']);
    Route::get('/blood-banks/{id}', [BloodBankController::class, 'show']);
    Route::post('/blood-banks', [BloodBankController::class, 'store']);
    Route::put('/blood-banks/{id}', [BloodBankController::class, 'update']);
    Route::delete('/blood-banks/{id}', [BloodBankController::class, 'destroy']);
    Route::get('/blood-banks/{id}/stocks', [StockController::class, 'getBloodBankStocks']);

    // User appointments routes
    Route::get('/users/{user}/rendez-vous', [RendezVousController::class, 'getUserAppointments']);
    Route::post('/rendez-vous/{rendezVous}/cancel', [RendezVousController::class, 'cancelAppointment']);

    // User intentions de dons routes
    Route::get('/users/{user}/intentions-de-dons', [IntentionDonController::class, 'getUserIntentions']);
    Route::post('/intentions-de-dons', [IntentionDonController::class, 'store']);
    Route::delete('/intentions-de-dons/{intentionDon}', [IntentionDonController::class, 'destroy']);

    // Blood Requests Routes
    Route::post('/demandes', [DemandeController::class, 'createBloodRequest']);
    Route::delete('/demandes/{id}', [DemandeController::class, 'deleteBloodRequest']);
}); 
