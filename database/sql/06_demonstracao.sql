set search_path to nelson_brasileirao_db, public;

-- dados da demonstração
select p.id_partida, m.nome as mandante, v.nome as visitante
  from partida p
  join clube m on m.id_clube = p.mandante_id
  join clube v on v.id_clube = p.visitante_id
 where p.id_partida = 4607;
select id_jogador, nome, clube_id from jogador where id_jogador = 21;

-- índice seqscan desligado só para mostrar o índice sendo usado
set enable_seqscan = off;
explain select * from cartao where partida_id = 4607 and jogador_id = 21;
reset enable_seqscan;

-- function
select * from fn_artilharia(2019, 10);
select * from fn_artilharia(null, 10);

-- procedure: o 2º amarelo vira vermelho
begin;
call sp_registrar_cartao(4607, 21, 10, 'amarelo', null);
call sp_registrar_cartao(4607, 21, 20, 'amarelo', null);
select * from cartao where partida_id = 4607 and jogador_id = 21;
-- trigger: jogador expulso não marca gol
insert into gol (minuto, partida_id, jogador_id) values (50, 4607, 21);   
rollback;

-- trigger: clube do gol preenchido sozinho
begin;
insert into gol (minuto, partida_id, jogador_id)           values (15, 4607, 21) returning *;
insert into gol (minuto, partida_id, jogador_id, clube_id) values (16, 4607, 21, 20) returning *;  
insert into gol (minuto, partida_id, jogador_id, clube_id) values (17, 4607, 21, 5);               
rollback;