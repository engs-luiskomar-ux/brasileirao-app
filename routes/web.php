<?php

use App\Http\Controllers\JogadorController;
use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('jogadores', JogadorController::class)
    ->parameters(['jogadores' => 'jogador']);

Route::post('jogadores/{jogador}/transferencias', [JogadorController::class, 'transferir'])
    ->name('jogadores.transferir');

// Rotas Joao Vitor
Route::resource('partidas', PartidaController::class)
    ->parameters(['partidas' => 'partida']);


// Rotas Thiago - início, gols, cartões e estatísticas
Route::get('/', \App\Http\Controllers\DashboardController::class)->name('dashboard');

Route::post('partidas/{partida}/gols', [\App\Http\Controllers\GolController::class, 'store'])->name('partidas.gols.store');
Route::delete('gols/{gol}', [\App\Http\Controllers\GolController::class, 'destroy'])->name('gols.destroy');

Route::post('partidas/{partida}/cartoes', [\App\Http\Controllers\CartaoController::class, 'store'])->name('partidas.cartoes.store');
Route::delete('cartoes/{cartao}', [\App\Http\Controllers\CartaoController::class, 'destroy'])->name('cartoes.destroy');

Route::get('estatisticas/artilharia', [\App\Http\Controllers\EstatisticaController::class, 'artilharia'])->name('estatisticas.artilharia');
Route::get('estatisticas/cartoes', [\App\Http\Controllers\EstatisticaController::class, 'cartoes'])->name('estatisticas.cartoes');