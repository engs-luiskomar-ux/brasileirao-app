<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::apiResource('jogadores', Api\JogadorController::class)
    ->parameters(['jogadores' => 'jogador']);

Route::post('jogadores/{jogador}/transferencias', [Api\JogadorController::class, 'transferir'])
    ->name('jogadores.transferir');

// Rotas João Vitor (API)
Route::apiResource('partidas', Api\PartidaController::class)
    ->parameters(['partidas' => 'partida'])
    ->names('api.partidas');


// Rotas Thiago (API)
Route::apiResource('gols', Api\GolController::class)
    ->parameters(['gols' => 'gol'])
    ->names('api.gols');

Route::post('partidas/{partida}/cartoes', [Api\CartaoController::class, 'store'])
    ->name('api.partidas.cartoes.store');

Route::get('artilharia', [Api\EstatisticaController::class, 'artilharia'])
    ->name('api.artilharia');