<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/02_luis_antonio.sql')));
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER   IF EXISTS trg_clube_normaliza ON clube;
            DROP FUNCTION  IF EXISTS fn_trg_clube_normaliza();
            DROP PROCEDURE IF EXISTS sp_criar_temporada(integer, varchar, integer);
            DROP FUNCTION  IF EXISTS fn_classificacao(integer);
            DROP INDEX     IF EXISTS idx_clube_nome_lower;
        SQL);
    }
};
