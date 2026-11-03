<?php

use App\Http\Controllers\ClassificacaoController;
use App\Http\Controllers\ClubeController;
use App\Http\Controllers\JogadorController;
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\TemporadaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('clubes', ClubeController::class)
    ->parameters(['clubes' => 'clube']);

Route::resource('temporadas', TemporadaController::class)
    ->except(['show'])
    ->parameters(['temporadas' => 'temporada']);

Route::get('classificacao', [ClassificacaoController::class, 'index'])
    ->name('classificacao.index');

Route::resource('jogadores', JogadorController::class)
    ->parameters(['jogadores' => 'jogador']);

Route::post('jogadores/{jogador}/transferencias', [JogadorController::class, 'transferir'])
    ->name('jogadores.transferir');

// Rotas Joao Vitor
Route::resource('partidas', PartidaController::class)
    ->parameters(['partidas' => 'partida']);
