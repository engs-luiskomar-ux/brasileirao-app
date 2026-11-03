<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('temporada')) {
            return;
        }

        Schema::create('temporada', function (Blueprint $table) {
            $table->integer('id_temporada')->generatedAs()->always()->primary();
            $table->integer('ano')->unique();
            $table->string('nome', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporada');
    }
};
