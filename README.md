# Brasileirão DB

Projeto do 2º bimestre de Banco de Dados II e Back-end.

Equipe: Luis Antonio, João Vitor, Nelson e Thiago.

Laravel 12, Blade e PostgreSQL no Neon. Tem clubes, temporadas, jogadores, transferências, partidas, gols, cartões e estatísticas, com telas e API.
Usamos o banco do Nelson do 1º bimestre porque ele já reúne clubes, jogadores, temporadas, partidas, gols e cartões do mesmo campeonato.

## Como rodar

Precisa de PHP 8.4 com `pdo_pgsql`, Composer e Git.

```bash
composer install
copy .env.example .env
php artisan key:generate
```

No Linux/macOS, use `cp` no lugar de `copy`. Preencha os dados do Neon no `.env`, com host sem `-pooler` e SSL obrigatório. Não envie esse arquivo ao GitHub.

```bash
php artisan db:verificar
php artisan migrate
php artisan serve
```

Abra http://localhost:8000. Não precisa de npm para essas telas.
O banco do grupo já existe. Para criar a estrutura em um banco vazio separado, configure o `.env` para esse banco e rode `php artisan migrate`. Para trazer os dados do 1º bimestre, veja [docs/BACKUP.md](docs/BACKUP.md).
Não use `migrate:fresh`, rollback ou restauração no banco compartilhado.

## Rotas

- `/clubes`: cadastro, listagem, detalhes, edição e exclusão.
- `/temporadas`: cadastro, listagem, edição e exclusão.
- `/classificacao?ano=2019`: classificação da temporada.
- `/jogadores`: cadastro e transferência de jogadores.
- `/partidas`: cadastro, placares e súmula com gols e cartões.
- `/estatisticas/artilharia` e `/estatisticas/cartoes`: rankings.
- `GET/POST /api/clubes`: consulta e cadastro em JSON.
- `GET/PUT/PATCH/DELETE /api/clubes/{id}`: operações de um clube.
- `GET /api/classificacao/{ano}`: classificação em JSON.
- `/api/jogadores`, `/api/partidas` e `/api/gols`: CRUD em JSON.
- `POST /api/jogadores/{id}/transferencias`: transferência.
- `POST /api/partidas/{id}/cartoes`: registrar cartão.
- `GET /api/artilharia?ano=2019`: artilheiros.

Os recursos CRUD da API ficam em `data`. Nas partidas, a data do jogo se chama `data_partida`.
No cadastro de gol, `contra: true` credita o adversário e não aumenta a artilharia do jogador. Em gols antigos, `contra: null` significa que o backup não informa o tipo.
O cartão vermelho gerado por dois amarelos é cancelado removendo um dos amarelos.

Exemplo de corpo para `POST /api/clubes` (envie `Content-Type: application/json`):

```json
{"nome":"Campo Real FC","cidade":"Guarapuava","estado":"PR","ano_fundacao":2000}
```

A API usa 200 para consultas/atualizações, 201 para cadastro, 204 para exclusão, 404 para registro ausente, 409 para conflito e 422 para validação.

## Branches

`main` é a base do grupo. Cada um trabalha na sua: `Luis`, `Joao`, `nelson` ou `Thiago`, e abre um pull request para a `main`. O Nelson usou a branch com inicial minúscula.

Luis: base, clubes, temporadas e classificação. João: partidas. Nelson: jogadores, transferências e backup. Thiago: gols, cartões, estatísticas e painel.

## Testes

Use PostgreSQL local em um banco separado chamado `brasileirao_test`. Os testes recriam as tabelas desse banco.

```bash
createdb -h 127.0.0.1 -U postgres brasileirao_test
copy .env.testing.example .env.testing
php artisan key:generate --env=testing
php artisan test
```

Ajuste usuário, senha e porta em `.env.testing`. A configuração dos testes força host local e o banco `brasileirao_test`.
Os testes usam as migrations oficiais dos quatro integrantes.

## Entrega

O relatório está em [docs/Relatorio_Projeto.pdf](docs/Relatorio_Projeto.pdf). As evidências ficam em `docs/evidencias/` e o roteiro da apresentação em [docs/APRESENTACAO.md](docs/APRESENTACAO.md).
