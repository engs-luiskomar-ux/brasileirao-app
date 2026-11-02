<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('historico_transferencia')) {
            return;
        }

        Schema::create('historico_transferencia', function (Blueprint $table) {
            $table->integer('id_transferencia')->generatedAs()->always()->primary();
            $table->integer('jogador_id');
            $table->integer('clube_origem_id');
            $table->integer('clube_destino_id');
            $table->timestamp('data_transferencia')->useCurrent();

            $table->foreign('jogador_id')->references('id_jogador')->on('jogador')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('clube_origem_id')->references('id_clube')->on('clube')->cascadeOnUpdate();
            $table->foreign('clube_destino_id')->references('id_clube')->on('clube')->cascadeOnUpdate();
        });

        DB::statement('ALTER TABLE historico_transferencia
            ADD CONSTRAINT historico_clubes_diferentes CHECK (clube_origem_id <> clube_destino_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_transferencia');
    }
};
