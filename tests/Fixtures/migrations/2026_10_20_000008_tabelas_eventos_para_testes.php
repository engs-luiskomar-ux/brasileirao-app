<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gol')) {
            Schema::create('gol', function (Blueprint $table) {
                $table->integer('id_gol')->generatedAs()->always()->primary();
                $table->integer('minuto');
                $table->integer('partida_id');
                $table->integer('jogador_id');
                $table->integer('clube_id')->nullable();
                $table->foreign('partida_id')->references('id_partida')->on('partida')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('jogador_id')->references('id_jogador')->on('jogador')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('clube_id')->references('id_clube')->on('clube');
                $table->index('minuto', 'idx_nelson_gol_minuto');
                $table->index('partida_id', 'idx_nelson_gol_partida');
            });
            DB::statement('ALTER TABLE gol ADD CONSTRAINT gol_minuto_check CHECK (minuto BETWEEN 0 AND 120)');
        }

        if (! Schema::hasTable('cartao')) {
            Schema::create('cartao', function (Blueprint $table) {
                $table->integer('id_cartao')->generatedAs()->always()->primary();
                $table->integer('minuto');
                $table->string('tipo', 8);
                $table->integer('partida_id');
                $table->integer('jogador_id');
                $table->foreign('partida_id')->references('id_partida')->on('partida')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreign('jogador_id')->references('id_jogador')->on('jogador')->cascadeOnUpdate()->cascadeOnDelete();
            });
            DB::statement('ALTER TABLE cartao ADD CONSTRAINT cartao_minuto_check CHECK (minuto BETWEEN 0 AND 120)');
            DB::statement("ALTER TABLE cartao ADD CONSTRAINT cartao_tipo_check CHECK (tipo IN ('amarelo', 'vermelho'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cartao');
        Schema::dropIfExists('gol');
    }
};
