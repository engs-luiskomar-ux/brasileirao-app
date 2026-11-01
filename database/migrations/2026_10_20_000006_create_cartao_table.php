<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Thiago - Tabela cartao, mesma estrutura do backup do 1º bimestre.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cartao')) {
            return;
        }

        Schema::create('cartao', function (Blueprint $table) {
            $table->integer('id_cartao')->generatedAs()->always()->primary();
            $table->integer('minuto');
            $table->string('tipo', 8);
            $table->integer('partida_id');
            $table->integer('jogador_id');

            $table->foreign('partida_id')->references('id_partida')->on('partida')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('jogador_id')->references('id_jogador')->on('jogador')->cascadeOnUpdate()->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE cartao ADD CONSTRAINT cartao_minuto_check CHECK (minuto >= 0 AND minuto <= 120)');
        DB::statement("ALTER TABLE cartao ADD CONSTRAINT cartao_tipo_check CHECK (tipo IN ('amarelo', 'vermelho'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('cartao');
    }
};