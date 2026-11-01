<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Thiago - lê o arquivo database/sql/05_thiago.sql e executa no banco.
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/05_thiago.sql')));
    }

    // apaga os 4 objetos criados no up
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            drop trigger   if exists trg_gol_valida on gol;
            drop function  if exists fn_trg_gol_valida();
            drop procedure if exists sp_registrar_cartao(integer, integer, integer, varchar, text);
            drop function  if exists fn_artilharia(integer, integer);
            drop index     if exists idx_cartao_partida_jogador;
        SQL);
    }
};