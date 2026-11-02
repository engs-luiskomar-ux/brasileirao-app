SET search_path TO nelson_brasileirao_db, public;

CREATE INDEX IF NOT EXISTS idx_partida_temporada_rodada
    ON partida (temporada_id, rodada);

CREATE OR REPLACE FUNCTION fn_placar_partida(p_partida_id integer)
RETURNS TABLE (gols_mandante integer, gols_visitante integer)
LANGUAGE sql
STABLE
SET search_path = nelson_brasileirao_db, public
AS $$
    SELECT count(g.id_gol) FILTER (WHERE g.clube_id = p.mandante_id)::integer,
           count(g.id_gol) FILTER (WHERE g.clube_id = p.visitante_id)::integer
      FROM partida p
      LEFT JOIN gol g ON g.partida_id = p.id_partida
     WHERE p.id_partida = p_partida_id
     GROUP BY p.id_partida, p.mandante_id, p.visitante_id;
$$;

CREATE OR REPLACE PROCEDURE sp_registrar_partida(
    IN    p_temporada_id integer,
    IN    p_rodada       integer,
    IN    p_data_partida timestamp,
    IN    p_mandante_id  integer,
    IN    p_visitante_id integer,
    INOUT p_id_partida   integer DEFAULT NULL
)
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_clube_ocupado varchar;
BEGIN
    IF p_mandante_id = p_visitante_id THEN
        RAISE EXCEPTION 'Mandante e visitante precisam ser clubes diferentes.';
    END IF;

    SELECT c.nome
      INTO v_clube_ocupado
      FROM partida p
      JOIN clube c ON c.id_clube IN (p_mandante_id, p_visitante_id)
     WHERE p.temporada_id = p_temporada_id
       AND p.rodada       = p_rodada
       AND c.id_clube IN (p.mandante_id, p.visitante_id)
     LIMIT 1;

    IF v_clube_ocupado IS NOT NULL THEN
        RAISE EXCEPTION 'O clube % já tem uma partida na rodada %.', v_clube_ocupado, p_rodada;
    END IF;

    INSERT INTO partida (rodada, data_partida, temporada_id, mandante_id, visitante_id)
    VALUES (p_rodada, p_data_partida, p_temporada_id, p_mandante_id, p_visitante_id)
    RETURNING id_partida INTO p_id_partida;
END;
$$;

CREATE OR REPLACE FUNCTION fn_trg_partida_valida()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_ano      integer;
    v_ano_jogo integer := extract(year  FROM NEW.data_partida)::integer;
    v_mes_jogo integer := extract(month FROM NEW.data_partida)::integer;
BEGIN
    SELECT ano INTO v_ano FROM temporada WHERE id_temporada = NEW.temporada_id;

    IF v_ano IS NULL THEN
        RAISE EXCEPTION 'Temporada % não encontrada.', NEW.temporada_id;
    END IF;

    IF NOT (v_ano_jogo = v_ano OR (v_ano_jogo = v_ano + 1 AND v_mes_jogo <= 3)) THEN
        RAISE EXCEPTION 'A data % está fora da temporada % (permitido: ano % ou até março de %).',
            to_char(NEW.data_partida, 'DD/MM/YYYY'), v_ano, v_ano, v_ano + 1;
    END IF;

    IF TG_OP = 'UPDATE'
       AND (NEW.mandante_id <> OLD.mandante_id OR NEW.visitante_id <> OLD.visitante_id)
       AND (EXISTS (SELECT 1 FROM gol    WHERE partida_id = OLD.id_partida)
         OR EXISTS (SELECT 1 FROM cartao WHERE partida_id = OLD.id_partida)) THEN
        RAISE EXCEPTION 'Não é possível trocar os clubes de uma partida que já tem gols ou cartões registrados.';
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_partida_valida ON partida;
CREATE TRIGGER trg_partida_valida
    BEFORE INSERT OR UPDATE ON partida
    FOR EACH ROW
    EXECUTE FUNCTION fn_trg_partida_valida();
