<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS nelson_brasileirao_db');
    }

    public function down(): void
    {
        // Mantém o schema, pois ele contém os dados reais do grupo.
    }
};
