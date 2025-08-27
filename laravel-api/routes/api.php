<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/my-profile', [AuthController::class, 'myProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'dashboardStatistic']);

    Route::prefix('ticket')->group(function() {
        Route::get('/all', [TicketController::class, 'allTickets']);
        Route::get('/my-ticket', [TicketController::class, 'myTicket']);
        Route::get('/detail', [TicketController::class, 'detailTicket']);

        Route::post('/store', [TicketController::class, 'storeTicket']);
        Route::post('/update', [TicketController::class, 'updateTicket']);

        Route::post('/reply', [TicketController::class, 'storeReplyTicket']);
    });
});
