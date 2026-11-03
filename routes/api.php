<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::apiResource('jogadores', Api\JogadorController::class)
    ->parameters(['jogadores' => 'jogador'])
    ->names('api.jogadores');

Route::post('jogadores/{jogador}/transferencias', [Api\JogadorController::class, 'transferir'])
    ->name('api.jogadores.transferir');

// Rotas João Vitor (API)
Route::apiResource('partidas', Api\PartidaController::class)
    ->parameters(['partidas' => 'partida'])
    ->names('api.partidas');
