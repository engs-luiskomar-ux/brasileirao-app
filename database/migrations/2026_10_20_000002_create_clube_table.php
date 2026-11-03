<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clube')) {
            return;
        }

        Schema::create('clube', function (Blueprint $table) {
            $table->integer('id_clube')->generatedAs()->always()->primary();
            $table->string('nome', 100)->unique();
            $table->string('cidade', 50);
            $table->char('estado', 2);
            $table->integer('ano_fundacao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clube');
    }
};
