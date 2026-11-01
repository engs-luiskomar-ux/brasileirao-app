set search_path to nelson_brasileirao_db, public;

-- indice composto: busca de cartoes por partida e jogador - sumula, procedure e trigger
create index if not exists idx_cartao_partida_jogador
    on cartao (partida_id, jogador_id);

-- function: devolve o ranking de artilheiros, por ano ou geral, se p_ano for null
create or replace function fn_artilharia(p_ano integer default null, p_limite integer default 20)
returns table (
    posicao    bigint,
    jogador_id integer,
    jogador    varchar,
    clubes     text,
    gols       integer
)
language sql
stable
set search_path = nelson_brasileirao_db, public
as $$
    select rank() over (order by count(*) desc),
           j.id_jogador,
           j.nome,
           string_agg(distinct c.nome, ' / '),
           count(*)::integer
      from gol g
      join jogador   j on j.id_jogador   = g.jogador_id
      join clube     c on c.id_clube     = g.clube_id
      join partida   p on p.id_partida   = g.partida_id
      join temporada t on t.id_temporada = p.temporada_id
     where p_ano is null or t.ano = p_ano
     group by j.id_jogador, j.nome
     order by count(*) desc, j.nome
     limit p_limite;
$$;


-- procedure: registra cartao, o segundo amarelo vira vermelho sozinho
create or replace procedure sp_registrar_cartao(
    in    p_partida_id integer,
    in    p_jogador_id integer,
    in    p_minuto     integer,
    in    p_tipo       varchar,
    inout p_mensagem   text default null
)
language plpgsql
set search_path = nelson_brasileirao_db, public
as $$
declare
    v_mandante  integer;
    v_visitante integer;
    v_clube     integer;
    v_amarelos  integer;
begin
    select mandante_id, visitante_id
      into v_mandante, v_visitante
      from partida
     where id_partida = p_partida_id;

    if not found then
        raise exception 'Partida % não encontrada.', p_partida_id;
    end if;

    select clube_id into v_clube from jogador where id_jogador = p_jogador_id;

    if not found then
        raise exception 'Jogador % não encontrado.', p_jogador_id;
    end if;

    if v_clube not in (v_mandante, v_visitante) then
        raise exception 'O jogador não pertence a nenhum dos clubes da partida.';
    end if;

    if p_tipo not in ('amarelo', 'vermelho') then
        raise exception 'Tipo de cartão inválido: %.', p_tipo;
    end if;

    if exists (select 1 from cartao
                where partida_id = p_partida_id
                  and jogador_id = p_jogador_id
                  and tipo = 'vermelho') then
        raise exception 'O jogador já foi expulso nesta partida.';
    end if;

    insert into cartao (minuto, tipo, partida_id, jogador_id)
    values (p_minuto, p_tipo, p_partida_id, p_jogador_id);

    p_mensagem := 'Cartão ' || p_tipo || ' registrado.';

    if p_tipo = 'amarelo' then
        select count(*) into v_amarelos
          from cartao
         where partida_id = p_partida_id
           and jogador_id = p_jogador_id
           and tipo = 'amarelo';

        if v_amarelos >= 2 then
            insert into cartao (minuto, tipo, partida_id, jogador_id)
            values (p_minuto, 'vermelho', p_partida_id, p_jogador_id);

            p_mensagem := 'Segundo amarelo: cartão vermelho aplicado automaticamente (jogador expulso).';
        end if;
    end if;
end;
$$;