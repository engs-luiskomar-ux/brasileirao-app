SET search_path TO nelson_brasileirao_db, public;

-- Guarda o tipo do gol sem deduzi-lo pelo clube atual de um jogador transferido.
CREATE OR REPLACE FUNCTION fn_trg_gol_valida()
RETURNS trigger LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_mandante integer;
    v_visitante integer;
    v_clube integer;
    v_recalcular boolean := TG_OP = 'INSERT';
BEGIN
    SELECT mandante_id, visitante_id INTO v_mandante, v_visitante
      FROM partida WHERE id_partida = NEW.partida_id FOR UPDATE;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Partida % não encontrada.', NEW.partida_id;
    END IF;

    IF TG_OP = 'UPDATE' THEN
        v_recalcular := NEW.jogador_id IS DISTINCT FROM OLD.jogador_id
                     OR NEW.partida_id IS DISTINCT FROM OLD.partida_id
                     OR NEW.contra IS DISTINCT FROM OLD.contra;
    END IF;
    IF v_recalcular THEN
        SELECT clube_id INTO v_clube FROM jogador WHERE id_jogador = NEW.jogador_id;
        IF v_clube IS NULL OR v_clube NOT IN (v_mandante, v_visitante) THEN
            RAISE EXCEPTION 'O jogador não pertence a nenhum dos clubes da partida.';
        END IF;
        IF NEW.contra IS TRUE THEN
            NEW.clube_id := CASE WHEN v_clube = v_mandante THEN v_visitante ELSE v_mandante END;
        ELSIF NEW.contra IS FALSE OR NEW.clube_id IS NULL THEN
            NEW.clube_id := v_clube;
        END IF;
    END IF;

    IF NEW.clube_id IS NULL OR NEW.clube_id NOT IN (v_mandante, v_visitante) THEN
        RAISE EXCEPTION 'O gol precisa ser creditado ao mandante ou ao visitante da partida.';
    END IF;
    IF EXISTS (SELECT 1 FROM cartao WHERE partida_id = NEW.partida_id
               AND jogador_id = NEW.jogador_id AND tipo = 'vermelho' AND minuto < NEW.minuto) THEN
        RAISE EXCEPTION 'O jogador foi expulso antes do minuto % e não pode ter marcado esse gol.', NEW.minuto;
    END IF;
    RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION fn_artilharia(p_ano integer DEFAULT NULL, p_limite integer DEFAULT 20)
RETURNS TABLE (posicao bigint, jogador_id integer, jogador varchar, clubes text, gols integer)
LANGUAGE sql STABLE
SET search_path = nelson_brasileirao_db, public
AS $$
    SELECT rank() OVER (ORDER BY count(*) DESC), j.id_jogador, j.nome,
           string_agg(DISTINCT c.nome, ' / '), count(*)::integer
      FROM gol g
      JOIN jogador j ON j.id_jogador = g.jogador_id
      JOIN clube c ON c.id_clube = g.clube_id
      JOIN partida p ON p.id_partida = g.partida_id
      JOIN temporada t ON t.id_temporada = p.temporada_id
     WHERE (p_ano IS NULL OR t.ano = p_ano) AND g.contra IS NOT TRUE
     GROUP BY j.id_jogador, j.nome
     ORDER BY count(*) DESC, j.nome
     LIMIT p_limite;
$$;

CREATE OR REPLACE FUNCTION fn_estatisticas_jogador(p_jogador_id integer)
RETURNS TABLE (gols integer, amarelos integer, vermelhos integer, temporadas_com_gol integer, idade integer)
LANGUAGE sql STABLE
SET search_path = nelson_brasileirao_db, public
AS $$
    SELECT (SELECT count(*) FROM gol g WHERE g.jogador_id = j.id_jogador AND g.contra IS NOT TRUE)::integer,
           (SELECT count(*) FROM cartao c WHERE c.jogador_id = j.id_jogador AND c.tipo = 'amarelo')::integer,
           (SELECT count(*) FROM cartao c WHERE c.jogador_id = j.id_jogador AND c.tipo = 'vermelho')::integer,
           (SELECT count(DISTINCT p.temporada_id) FROM gol g JOIN partida p ON p.id_partida = g.partida_id
             WHERE g.jogador_id = j.id_jogador AND g.contra IS NOT TRUE)::integer,
           date_part('year', age(current_date, j.data_nascimento))::integer
      FROM jogador j WHERE j.id_jogador = p_jogador_id;
$$;

CREATE OR REPLACE PROCEDURE sp_registrar_cartao(
    IN p_partida_id integer, IN p_jogador_id integer, IN p_minuto integer,
    IN p_tipo varchar, INOUT p_mensagem text DEFAULT NULL
)
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_mandante integer;
    v_visitante integer;
    v_clube integer;
    v_amarelos integer;
    v_ultimo_amarelo integer;
    v_expulsao integer;
BEGIN
    -- O mesmo bloqueio é usado pela trigger do gol para manter a súmula consistente.
    SELECT mandante_id, visitante_id INTO v_mandante, v_visitante
      FROM partida WHERE id_partida = p_partida_id FOR UPDATE;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Partida % não encontrada.', p_partida_id;
    END IF;
    SELECT clube_id INTO v_clube FROM jogador WHERE id_jogador = p_jogador_id;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Jogador % não encontrado.', p_jogador_id;
    END IF;
    IF v_clube NOT IN (v_mandante, v_visitante) THEN
        RAISE EXCEPTION 'O jogador não pertence a nenhum dos clubes da partida.';
    END IF;
    IF p_tipo IS NULL OR p_tipo NOT IN ('amarelo', 'vermelho') THEN
        RAISE EXCEPTION 'Tipo de cartão inválido: %.', p_tipo;
    END IF;
    IF p_minuto IS NULL OR p_minuto NOT BETWEEN 0 AND 120 THEN
        RAISE EXCEPTION 'O minuto precisa estar entre 0 e 120.';
    END IF;
    IF EXISTS (SELECT 1 FROM cartao WHERE partida_id = p_partida_id
               AND jogador_id = p_jogador_id AND tipo = 'vermelho') THEN
        RAISE EXCEPTION 'O jogador já foi expulso nesta partida.';
    END IF;

    SELECT count(*), max(minuto) INTO v_amarelos, v_ultimo_amarelo
      FROM cartao WHERE partida_id = p_partida_id AND jogador_id = p_jogador_id AND tipo = 'amarelo';
    IF p_tipo = 'vermelho' THEN
        v_expulsao := p_minuto;
    ELSIF v_amarelos >= 1 THEN
        v_expulsao := greatest(p_minuto, v_ultimo_amarelo);
    END IF;
    IF v_expulsao IS NOT NULL AND EXISTS (
        SELECT 1 FROM gol WHERE partida_id = p_partida_id AND jogador_id = p_jogador_id AND minuto > v_expulsao
    ) THEN
        RAISE EXCEPTION 'Já existe gol deste jogador após o minuto da expulsão. Corrija o gol antes de registrar o cartão.';
    END IF;

    INSERT INTO cartao (minuto, tipo, partida_id, jogador_id)
    VALUES (p_minuto, p_tipo, p_partida_id, p_jogador_id);
    p_mensagem := 'Cartão ' || p_tipo || ' registrado.';
    IF p_tipo = 'amarelo' AND v_amarelos >= 1 THEN
        INSERT INTO cartao (minuto, tipo, partida_id, jogador_id, automatico)
        VALUES (v_expulsao, 'vermelho', p_partida_id, p_jogador_id, true);
        p_mensagem := 'Segundo amarelo: cartão vermelho aplicado automaticamente (jogador expulso).';
    END IF;
END;
$$;
