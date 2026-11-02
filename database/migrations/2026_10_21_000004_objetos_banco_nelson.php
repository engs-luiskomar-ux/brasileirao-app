<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/04_nelson.sql')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER   IF EXISTS trg_jogador_transferencia ON jogador;
            DROP FUNCTION  IF EXISTS fn_trg_jogador_transferencia();
            DROP PROCEDURE IF EXISTS sp_transferir_jogador(integer, integer, integer);
            DROP FUNCTION  IF EXISTS fn_estatisticas_jogador(integer);
            DROP INDEX     IF EXISTS idx_jogador_clube;
        SQL);
    }
};
