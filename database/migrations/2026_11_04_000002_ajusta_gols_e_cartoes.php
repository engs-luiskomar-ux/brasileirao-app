<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('gol', 'contra')) {
            Schema::table('gol', function (Blueprint $table) {
                // NULL: o backup antigo não informa se o gol foi contra.
                $table->boolean('contra')->nullable();
            });
        }
        if (! Schema::hasColumn('cartao', 'automatico')) {
            Schema::table('cartao', function (Blueprint $table) {
                $table->boolean('automatico')->default(false);
            });
        }
        DB::unprepared(file_get_contents(database_path('sql/08_ajustes_eventos.sql')));
    }

    public function down(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/04_nelson.sql')));
        DB::unprepared(file_get_contents(database_path('sql/05_thiago.sql')));
        Schema::table('gol', fn (Blueprint $table) => $table->dropColumn('contra'));
        Schema::table('cartao', fn (Blueprint $table) => $table->dropColumn('automatico'));
    }
};
