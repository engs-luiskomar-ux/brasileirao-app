SET search_path TO nelson_brasileirao_db, public;

-- Padroniza a posição cadastrada com erro de digitação.
UPDATE jogador SET posicao = 'Zagueiro' WHERE posicao = 'Zagueira';

-- regras de check

ALTER TABLE jogador DROP CONSTRAINT IF EXISTS jogador_posicao_check;
ALTER TABLE jogador ADD  CONSTRAINT jogador_posicao_check
    CHECK (posicao IN ('Goleiro', 'Zagueiro', 'Lateral', 'Meio-campo', 'Atacante'));

ALTER TABLE jogador DROP CONSTRAINT IF EXISTS jogador_numero_camisa_check;
ALTER TABLE jogador ADD  CONSTRAINT jogador_numero_camisa_check
    CHECK (numero_camisa IS NULL OR numero_camisa BETWEEN 1 AND 99);

ALTER TABLE clube DROP CONSTRAINT IF EXISTS clube_estado_check;
ALTER TABLE clube ADD  CONSTRAINT clube_estado_check
    CHECK (estado ~ '^[A-Z]{2}$');

ALTER TABLE partida DROP CONSTRAINT IF EXISTS partida_rodada_check;
ALTER TABLE partida ADD  CONSTRAINT partida_rodada_check
    CHECK (rodada BETWEEN 1 AND 38);

-- tabela historico_transferencia

CREATE TABLE IF NOT EXISTS historico_transferencia (
    id_transferencia   integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    jogador_id         integer   NOT NULL REFERENCES jogador (id_jogador) ON UPDATE CASCADE ON DELETE CASCADE,
    clube_origem_id    integer   NOT NULL REFERENCES clube (id_clube)     ON UPDATE CASCADE,
    clube_destino_id   integer   NOT NULL REFERENCES clube (id_clube)     ON UPDATE CASCADE,
    data_transferencia timestamp NOT NULL DEFAULT now(),
    CONSTRAINT historico_clubes_diferentes CHECK (clube_origem_id <> clube_destino_id)
);
