SET search_path TO nelson_brasileirao_db, public;

CREATE OR REPLACE FUNCTION fn_trg_partida_valida()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_ano integer;
    v_ano_jogo integer := extract(year FROM NEW.data_partida)::integer;
    v_mes_jogo integer := extract(month FROM NEW.data_partida)::integer;
    v_clube_ocupado varchar;
BEGIN
    -- Serializa as alterações da temporada antes de conferir a rodada.
    SELECT ano INTO v_ano FROM temporada WHERE id_temporada = NEW.temporada_id FOR UPDATE;

    IF v_ano IS NULL THEN
        RAISE EXCEPTION 'Temporada % não encontrada.', NEW.temporada_id;
    END IF;

    IF NOT (v_ano_jogo = v_ano OR (v_ano_jogo = v_ano + 1 AND v_mes_jogo <= 3)) THEN
        RAISE EXCEPTION 'A data % está fora da temporada % (permitido: ano % ou até março de %).',
            to_char(NEW.data_partida, 'DD/MM/YYYY'), v_ano, v_ano, v_ano + 1;
    END IF;

    IF TG_OP = 'UPDATE'
       AND (NEW.mandante_id <> OLD.mandante_id OR NEW.visitante_id <> OLD.visitante_id)
       AND (EXISTS (SELECT 1 FROM gol WHERE partida_id = OLD.id_partida)
         OR EXISTS (SELECT 1 FROM cartao WHERE partida_id = OLD.id_partida)) THEN
        RAISE EXCEPTION 'Não é possível trocar os clubes de uma partida que já tem gols ou cartões registrados.';
    END IF;

    -- A procedure já conferia o cadastro; a trigger protege também a edição.
    SELECT c.nome INTO v_clube_ocupado
      FROM partida p
      JOIN clube c ON c.id_clube IN (NEW.mandante_id, NEW.visitante_id)
     WHERE p.temporada_id = NEW.temporada_id
       AND p.rodada = NEW.rodada
       AND p.id_partida IS DISTINCT FROM NEW.id_partida
       AND c.id_clube IN (p.mandante_id, p.visitante_id)
     ORDER BY c.id_clube
     LIMIT 1;

    IF v_clube_ocupado IS NOT NULL THEN
        RAISE EXCEPTION 'O clube % já tem uma partida na rodada %.', v_clube_ocupado, NEW.rodada;
    END IF;

    RETURN NEW;
END;
$$;
