SET search_path TO nelson_brasileirao_db;

-- Índice para consultas pelo nome do clube em letras minúsculas.
CREATE INDEX IF NOT EXISTS idx_clube_nome_lower
    ON clube (lower(nome) varchar_pattern_ops);
