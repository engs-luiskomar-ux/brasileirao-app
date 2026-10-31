<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('jogador')) {
            return;
        }

        Schema::create('jogador', function (Blueprint $table) {
            $table->integer('id_jogador')->generatedAs()->always()->primary();
            $table->string('nome', 100);
            $table->string('posicao', 50);
            $table->date('data_nascimento');
            $table->integer('numero_camisa')->nullable();
            $table->integer('clube_id');

            $table->foreign('clube_id')->references('id_clube')->on('clube')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jogador');
    }
};
