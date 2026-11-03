<?php

use App\Http\Controllers\ClubeController;
use App\Http\Controllers\JogadorController;
use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('clubes', ClubeController::class)
    ->parameters(['clubes' => 'clube']);

Route::resource('jogadores', JogadorController::class)
    ->parameters(['jogadores' => 'jogador']);

Route::post('jogadores/{jogador}/transferencias', [JogadorController::class, 'transferir'])
    ->name('jogadores.transferir');

// Rotas Joao Vitor
Route::resource('partidas', PartidaController::class)
    ->parameters(['partidas' => 'partida']);
