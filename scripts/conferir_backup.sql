SET search_path TO nelson_brasileirao_db, public;
SELECT 'clube' AS tabela, count(*) AS registros FROM clube
UNION ALL SELECT 'temporada', count(*) FROM temporada
UNION ALL SELECT 'jogador', count(*) FROM jogador
UNION ALL SELECT 'partida', count(*) FROM partida
UNION ALL SELECT 'gol', count(*) FROM gol
UNION ALL SELECT 'cartao', count(*) FROM cartao
UNION ALL SELECT 'historico_transferencia', count(*) FROM historico_transferencia
ORDER BY tabela;
SELECT routine_type, routine_name FROM information_schema.routines
WHERE routine_schema = 'nelson_brasileirao_db' ORDER BY 1, 2;
SELECT tgname FROM pg_trigger WHERE NOT tgisinternal
AND tgrelid IN (SELECT oid FROM pg_class WHERE relnamespace = 'nelson_brasileirao_db'::regnamespace)
ORDER BY 1;
SELECT indexname FROM pg_indexes WHERE schemaname = 'nelson_brasileirao_db' ORDER BY 1;
