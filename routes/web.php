<?php

use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// Rotas João Vitor
Route::resource('partidas', PartidaController::class)
    ->parameters(['partidas' => 'partida']);
// Rotas João Vitor (API)
Route::apiResource('partidas', App\Http\Controllers\Api\PartidaController::class)
    ->parameters(['partidas' => 'partida'])
    ->names('api.partidas');