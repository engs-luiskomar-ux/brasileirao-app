<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
 
    public function up(): void
    {
       DB::unprepared(file_get_contents(database_path('sql/03_joao_vitor.sql')));
    }

   
  
    public function down(): void
    {
             DB::unprepared(<<<'SQL'
            DROP TRIGGER   IF EXISTS trg_partida_valida ON partida;
            DROP FUNCTION  IF EXISTS fn_trg_partida_valida();
            DROP PROCEDURE IF EXISTS sp_registrar_partida(integer, integer, timestamp, integer, integer, integer);
            DROP FUNCTION  IF EXISTS fn_placar_partida(integer);
            DROP INDEX     IF EXISTS idx_partida_temporada_rodada;
        SQL);  
    }
};
