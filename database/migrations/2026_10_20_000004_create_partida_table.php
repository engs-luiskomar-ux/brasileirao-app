<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('partida')){
            return;
        }
        Schema::create(
            'partida', function (Blueprint $table) {
            $table->integer('id_partida')->generatedAs()->always()->primary();
            $table->integer('rodada');
            $table->timestamp('data_partida');
            $table->integer('temporada_id');
            $table->integer('mandante_id');
            $table->integer('visitante_id');

            $table->foreign('temporada_id')->references('id_temporada')->on('temporada')->cascadeOnUpdate();
             $table->foreign('mandante_id')->references('id_clube')->on('clube')->cascadeOnUpdate();
              $table->foreign('visitante_id')->references('id_clube')->on('clube')->cascadeOnUpdate();

            });
                // Check: Um clube não joga com ele mesmo
            DB::statement('ALTER TABLE partida add constraint partida_check Check (mandante_id <> visitante_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('partida');
    }
};
