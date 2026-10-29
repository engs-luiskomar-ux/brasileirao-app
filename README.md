# Brasileirão DB

Projeto do 2º bimestre de Banco de Dados II e Back-end.

Equipe: Luis Antonio, João Vitor, Nelson e Thiago.

Base em Laravel 12, Blade e PostgreSQL no Neon. Por enquanto, temos a estrutura inicial e a configuração da conexão. Os módulos serão feitos nas próximas etapas.

## Como rodar

Precisa de PHP 8.4 com `pdo_pgsql`, Composer e Git.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

No Windows, pode usar `copy .env.example .env`.
Preencha os dados do Neon no `.env`, usando o host sem `-pooler` e SSL obrigatório. A senha não vai para o GitHub.

```bash
php artisan db:verificar
php artisan serve
```

Abra http://localhost:8000. Não precisa rodar npm para a página inicial.
O banco já existe. Não rode `migrate:fresh`, `db:wipe` ou restaurações nele.

## Branches

`main` é a base do grupo. Cada um trabalha na sua: `Luis`, `Joao`, `Nelson` ou `Thiago`, e abre um pull request para a `main`.

Luis: base, clubes, temporadas e classificação. João: partidas. Nelson: jogadores, transferências e backup. Thiago: gols, cartões, estatísticas e painel.

## Testes

```bash
composer test
```

Os testes atuais verificam a base e não acessam o Neon. Para os futuros testes de banco, copie `.env.testing.example` para `.env.testing`, gere a chave com `php artisan key:generate --env=testing` e use um PostgreSQL separado chamado `brasileirao_test`.
