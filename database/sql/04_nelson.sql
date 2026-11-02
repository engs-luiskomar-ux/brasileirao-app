SET search_path TO nelson_brasileirao_db, public;

CREATE INDEX IF NOT EXISTS idx_jogador_clube
    ON jogador (clube_id);


CREATE OR REPLACE FUNCTION fn_estatisticas_jogador(p_jogador_id integer)
RETURNS TABLE (
    gols               integer,
    amarelos           integer,
    vermelhos          integer,
    temporadas_com_gol integer,
    idade              integer
)
LANGUAGE sql
STABLE
SET search_path = nelson_brasileirao_db, public
AS $$
    SELECT (SELECT count(*) FROM gol g WHERE g.jogador_id = j.id_jogador)::integer,
           (SELECT count(*) FROM cartao c WHERE c.jogador_id = j.id_jogador AND c.tipo = 'amarelo')::integer,
           (SELECT count(*) FROM cartao c WHERE c.jogador_id = j.id_jogador AND c.tipo = 'vermelho')::integer,
           (SELECT count(DISTINCT p.temporada_id)
              FROM gol g
              JOIN partida p ON p.id_partida = g.partida_id
             WHERE g.jogador_id = j.id_jogador)::integer,
           date_part('year', age(current_date, j.data_nascimento))::integer
      FROM jogador j
     WHERE j.id_jogador = p_jogador_id;
$$;


CREATE OR REPLACE PROCEDURE sp_transferir_jogador(
    IN p_jogador_id       integer,
    IN p_clube_destino_id integer,
    IN p_numero_camisa    integer DEFAULT NULL
)
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
DECLARE
    v_nome        varchar;
    v_clube_atual integer;
    v_numero      integer;
BEGIN
    SELECT nome, clube_id, numero_camisa
      INTO v_nome, v_clube_atual, v_numero
      FROM jogador
     WHERE id_jogador = p_jogador_id
       FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Jogador % não encontrado.', p_jogador_id;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM clube WHERE id_clube = p_clube_destino_id) THEN
        RAISE EXCEPTION 'Clube de destino % não encontrado.', p_clube_destino_id;
    END IF;

    IF v_clube_atual = p_clube_destino_id THEN
        RAISE EXCEPTION '% já pertence a esse clube.', v_nome;
    END IF;

    IF p_numero_camisa IS NOT NULL THEN
        IF EXISTS (SELECT 1 FROM jogador
                    WHERE clube_id = p_clube_destino_id
                      AND numero_camisa = p_numero_camisa) THEN
            RAISE EXCEPTION 'A camisa % já está em uso no clube de destino.', p_numero_camisa;
        END IF;
        v_numero := p_numero_camisa;
    ELSIF v_numero IS NOT NULL
          AND EXISTS (SELECT 1 FROM jogador
                       WHERE clube_id = p_clube_destino_id
                         AND numero_camisa = v_numero) THEN
        v_numero := NULL;
    END IF;

    UPDATE jogador
       SET clube_id      = p_clube_destino_id,
           numero_camisa = v_numero
     WHERE id_jogador = p_jogador_id;
END;
$$;


CREATE OR REPLACE FUNCTION fn_trg_jogador_transferencia()
RETURNS trigger
LANGUAGE plpgsql
SET search_path = nelson_brasileirao_db, public
AS $$
BEGIN
    INSERT INTO historico_transferencia (jogador_id, clube_origem_id, clube_destino_id)
    VALUES (NEW.id_jogador, OLD.clube_id, NEW.clube_id);

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_jogador_transferencia ON jogador;
CREATE TRIGGER trg_jogador_transferencia
    AFTER UPDATE OF clube_id ON jogador
    FOR EACH ROW
    WHEN (OLD.clube_id IS DISTINCT FROM NEW.clube_id)
    EXECUTE FUNCTION fn_trg_jogador_transferencia();
