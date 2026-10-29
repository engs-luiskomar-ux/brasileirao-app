<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('db:verificar', function () {
    try {
        $banco = DB::selectOne('SELECT current_database() AS banco, current_schema() AS schema');
        $esperado = trim(explode(',', config('database.connections.pgsql.search_path'))[0]);

        if ($banco->schema !== $esperado) {
            $this->error('Conectado, mas o schema do projeto não foi encontrado. Confira DB_SEARCH_PATH.');

            return 1;
        }

        if (! in_array(config('database.connections.pgsql.sslmode'), ['require', 'verify-ca', 'verify-full'], true)) {
            $this->error('A conexão precisa usar SSL. Confira DB_SSLMODE.');

            return 1;
        }

        $this->info('Conexão com PostgreSQL confirmada (SSL obrigatório).');
        $this->line('Banco: '.$banco->banco);
        $this->line('Schema: '.$banco->schema);

        return 0;
    } catch (Throwable $e) {
        // Não exibe a exceção, que pode conter dados da conexão.
        $this->error('Não foi possível conectar ao banco. Confira os dados do .env e o acesso ao Neon.');

        return 1;
    }
})->purpose('Confere a conexão e o schema do Neon sem alterar dados');
