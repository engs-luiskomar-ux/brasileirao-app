<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Rotas João Vitor (API)
Route::apiResource('partidas', Api\PartidaController::class)
    ->parameters(['partidas' => 'partida'])
    ->names('api.partidas');