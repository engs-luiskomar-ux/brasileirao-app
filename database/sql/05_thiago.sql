set search_path to nelson_brasileirao_db, public;

-- indice composto: busca de cartoes por partida e jogador - sumula, procedure e trigger
create index if not exists idx_cartao_partida_jogador
    on cartao (partida_id, jogador_id);

-- function: ranking de artilheiros. p_ano null = todas as temporadas
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