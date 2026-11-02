<?php

use App\Http\Controllers\JogadorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});










Route::resource('jogadores', JogadorController::class)
    ->parameters(['jogadores' => 'jogador']);

Route::post('jogadores/{jogador}/transferir', [JogadorController::class, 'transferir'])
    ->name('jogadores.transferir');