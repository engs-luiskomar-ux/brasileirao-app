# Brasileirão DB

Projeto do 2º bimestre de Banco de Dados II e Back-end.

Equipe: Luis Antonio, João Vitor, Nelson e Thiago.

Laravel 12, Blade e PostgreSQL no Neon. A parte do Luis tem clubes, temporadas e classificação, com telas e API.
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
php artisan serve
```

Abra http://localhost:8000/clubes. Não precisa de npm para essas telas.
O banco do grupo já existe e tem os objetos SQL. Para instalar em um banco vazio, primeiro integre as migrations de gols e cartões do Thiago. Não use `migrate:fresh`, rollback ou restauração no banco compartilhado.

## Rotas

- `/clubes`: cadastro, listagem, detalhes, edição e exclusão.
- `/temporadas`: cadastro, listagem, edição e exclusão.
- `/classificacao?ano=2019`: classificação da temporada.
- `GET/POST /api/clubes`: consulta e cadastro em JSON.
- `GET/PUT/PATCH/DELETE /api/clubes/{id}`: operações de um clube.
- `GET /api/classificacao/{ano}`: classificação em JSON.

Exemplo de corpo para `POST /api/clubes` (envie `Content-Type: application/json`):

```json
{"nome":"Campo Real FC","cidade":"Guarapuava","estado":"PR","ano_fundacao":2000}
```

A API usa 200 para consultas/atualizações, 201 para cadastro, 204 para exclusão, 404 para registro ausente, 409 para conflito e 422 para validação.

## Branches

`main` é a base do grupo. Cada um trabalha na sua: `Luis`, `Joao`, `Nelson` ou `Thiago`, e abre um pull request para a `main`.

Luis: base, clubes, temporadas e classificação. João: partidas. Nelson: jogadores, transferências e backup. Thiago: gols, cartões, estatísticas e painel.

## Testes do Luis

Use PostgreSQL local em um banco separado chamado `brasileirao_test`. Os testes recriam as tabelas desse banco.

```bash
createdb -h 127.0.0.1 -U postgres brasileirao_test
copy .env.testing.example .env.testing
php artisan key:generate --env=testing
php artisan test --filter="ClubeWebTest|ClubeApiTest|BaseTest"
```

Ajuste usuário, senha e porta em `.env.testing`. A configuração dos testes força host local e o banco `brasileirao_test`.
Há uma migration de apoio em `tests/Fixtures/migrations` para as tabelas de eventos que ainda não vieram da branch do Thiago; ela só é usada nos testes e não substitui as migrations oficiais.

`php artisan test` executa também os testes dos colegas. As pendências da revisão estão em [docs/REVISAO_LUIS.md](docs/REVISAO_LUIS.md).
