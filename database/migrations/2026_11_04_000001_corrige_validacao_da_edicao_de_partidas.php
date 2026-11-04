<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/07_revisao_partidas.sql')));
    }

    public function down(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/03_joao_vitor.sql')));
    }
};
