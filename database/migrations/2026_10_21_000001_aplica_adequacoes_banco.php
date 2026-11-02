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
        DB::unprepared(file_get_contents(database_path('sql/01_adequacoes_nelson.sql')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER TABLE jogador DROP CONSTRAINT IF EXISTS jogador_posicao_check;
            ALTER TABLE jogador DROP CONSTRAINT IF EXISTS jogador_numero_camisa_check;
            ALTER TABLE clube   DROP CONSTRAINT IF EXISTS clube_estado_check;
            ALTER TABLE partida DROP CONSTRAINT IF EXISTS partida_rodada_check;
        SQL);
    }
};
