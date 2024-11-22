<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Middleware\ConferirAgendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/register', function (Request $request) {
//     return 'oi';
// })->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/reserva/store', [ReservaController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('admin')->prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::post('/ambiente/store', [AmbienteController::class, 'store']);
        Route::get('/ambiente/index', [AmbienteController::class, 'index']);
        Route::delete('/ambiente/{ambiente}/delete', [AmbienteController::class, 'destroy']);

        Route::get('/reserva/index', [ReservaController::class, 'index']);
        Route::delete('/reserva/{reserva}/delete', [ReservaController::class, 'destroy']);
        Route::put('/reserva/{reserva}/update', [ReservaController::class, 'update']);
    });

    Route::name('professor')->prefix('professor')->middleware(['role:professor'])->group(function () {

        Route::post('/reserva/store', [ReservaController::class, 'store'])->middleware(ConferirAgendamento::class);
        Route::get('/reserva/index', [ReservaController::class, 'index']);
        Route::delete('/reserva/{reserva}/delete', [ReservaController::class, 'destroy']);
        Route::put('/reserva/{reserva}/update', [ReservaController::class, 'update']);
    });
});
