SET search_path TO nelson_brasileirao_db;

-- Índice para consultas pelo nome do clube em letras minúsculas.
CREATE INDEX IF NOT EXISTS idx_clube_nome_lower
    ON clube (lower(nome) varchar_pattern_ops);

-- Classificação por ano
CREATE OR REPLACE FUNCTION fn_classificacao(p_ano integer)
RETURNS TABLE (
    posicao        bigint,
    clube_id        integer,
    clube           varchar,
    pontos          integer,
    jogos           integer,
    vitorias        integer,
    empates         integer,
    derrotas        integer,
    gols_pro        integer,
    gols_contra     integer,
    saldo_gols      integer,
    aproveitamento  numeric
)
LANGUAGE sql
STABLE
SET search_path = nelson_brasileirao_db, public
AS $$
    WITH placar AS (
        SELECT p.id_partida,
               p.mandante_id,
               p.visitante_id,
               count(g.id_gol) FILTER (WHERE g.clube_id = p.mandante_id)  AS gm,
               count(g.id_gol) FILTER (WHERE g.clube_id = p.visitante_id) AS gv
          FROM partida p
          JOIN temporada t ON t.id_temporada = p.temporada_id
          LEFT JOIN gol g  ON g.partida_id   = p.id_partida
         WHERE t.ano = p_ano
           AND p.data_partida <= now()
         GROUP BY p.id_partida, p.mandante_id, p.visitante_id
    ),
    linhas AS (
        SELECT mandante_id  AS id_clube, gm AS gp, gv AS gc FROM placar
        UNION ALL
        SELECT visitante_id AS id_clube, gv AS gp, gm AS gc FROM placar
    ),
    tabela AS (
        /* Case para calculo de ponntos*/
        SELECT l.id_clube,
               sum(CASE WHEN l.gp > l.gc THEN 3 WHEN l.gp = l.gc THEN 1 ELSE 0 END)::int AS pts,
               count(*)::int                             AS j,
               count(*) FILTER (WHERE l.gp > l.gc)::int   AS v,
               count(*) FILTER (WHERE l.gp = l.gc)::int   AS e,
               count(*) FILTER (WHERE l.gp < l.gc)::int   AS d,
               sum(l.gp)::int                            AS gp,
               sum(l.gc)::int                            AS gc
          FROM linhas l
         GROUP BY l.id_clube
    )
    SELECT row_number() OVER (ORDER BY t.pts DESC, t.v DESC, (t.gp - t.gc) DESC, t.gp DESC, c.nome),
           c.id_clube,
           c.nome,
           t.pts, t.j, t.v, t.e, t.d, t.gp, t.gc,
           (t.gp - t.gc),
           round(t.pts * 100.0 / NULLIF(t.j * 3, 0), 1)
      FROM tabela t
      JOIN clube c ON c.id_clube = t.id_clube
     ORDER BY 1;
$$;

-- Procedure que cadastra uma temporada e devolve o identificador criado
CREATE OR REPLACE PROCEDURE sp_criar_temporada(
    IN p_ano integer,
    IN p_nome varchar DEFAULT NULL,
    INOUT p_id_temporada integer DEFAULT NULL
)
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_ano_maximo integer := extract(year FROM current_date)::integer + 1;
BEGIN
    IF p_ano IS NULL OR p_ano < 1971 OR p_ano > v_ano_maximo THEN
        RAISE EXCEPTION 'Ano % inválido: informe um ano entre 1971 e %.', p_ano, v_ano_maximo;
    END IF;

    IF EXISTS (SELECT 1 FROM temporada WHERE ano = p_ano) THEN
        RAISE EXCEPTION 'A temporada % já está cadastrada.', p_ano;
    END IF;

    INSERT INTO temporada (ano, nome)
    VALUES (p_ano, COALESCE(NULLIF(trim(p_nome), ''), 'Brasileirão ' || p_ano))
    RETURNING id_temporada INTO p_id_temporada;
END;
$$;
