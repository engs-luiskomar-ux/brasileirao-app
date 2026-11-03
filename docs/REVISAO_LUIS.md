# Revisão da parte do Luis

Conferência em 03/11/2026, na branch `Luis`. A parte do Luis foi integrada à `main` na mesma data, preservando os 30 commits.

## O que foi concluído

- Tratamento dos erros do PostgreSQL nas telas e na API.
- Layout, CSS, alertas, paginação e datas em português.
- Migrations, models, factories, validações, services e controllers de clubes e temporadas.
- Cadastro, listagem, detalhes, edição e exclusão de clubes.
- Cadastro de temporadas pela procedure, listagem, edição e exclusão.
- Classificação por temporada e campanha do clube pela function.
- API REST de clubes e endpoint de classificação.
- Índice `idx_clube_nome_lower`, function `fn_classificacao`, procedure `sp_criar_temporada` e trigger `trg_clube_normaliza`.
- README com instalação, rotas, exemplo da API e configuração dos testes.

## Testes

Comando usado para a parte do Luis:

```bash
php artisan test --filter="ClubeWebTest|ClubeApiTest|BaseTest"
```

Resultado: **22 testes passaram, com 110 verificações**. São 9 testes web, 10 da API e 3 da base.
Foram executados em PostgreSQL 18 local temporário, com banco `brasileirao_test`, migrations reais e operações HTTP. Os testes não gravam no Neon.

As tabelas de gols e cartões são preparadas por `tests/Fixtures/migrations` enquanto as migrations oficiais do Thiago não estão integradas nesta branch. A fixture respeita as tabelas oficiais quando elas já existem e só é carregada pelos testes do Luis.

O teste a partir de banco vazio identificou um erro no script do Luis: o `SET search_path` removia `public`, onde o Laravel havia criado a tabela de migrations. Foi corrigido para `nelson_brasileirao_db, public`.

A suíte completa foi executada também: **34 testes, 122 verificações, 3 erros e 7 falhas**. Os 10 problemas estão nos testes de jogadores e partidas. A suíte do grupo ainda precisa ficar verde antes da entrega.

## Conferência no Neon

Acesso somente de leitura, sem aplicar migrations ou alterar registros.

Responderam HTTP 200: `/clubes`, `/temporadas`, `/classificacao?ano=2019`, `/api/clubes?busca=fla`, `/api/classificacao/2019` e os detalhes do clube consultado.
Na classificação de 2019, a function retornou Flamengo com 90 pontos e 38 jogos na primeira posição.

Os quatro índices do projeto, quatro functions de negócio, quatro procedures e quatro triggers já estão instalados no schema do grupo. Isso atende à quantidade mínima de objetos de banco do enunciado.

## Situação das branches

| Integrante | Integração na main | O que falta |
|---|---|---|
| Luis | Concluída | Parte implementada e testes próprios passando |
| João | Seus commits já estavam na main | Corrigir os problemas de partidas encontrados na suíte completa |
| Nelson | Seus commits da branch `nelson` já estavam na main | Corrigir jogadores e incluir backup/restauração e o SQL original |
| Thiago | 21 commits ainda fora da main | Integrar gols, cartões, estatísticas e painel; incluir os testes de eventos e o SQL de demonstração |

A comparação com os arquivos de referência encontrou todos os arquivos de implementação previstos do João, com um nome de migration diferente. No Nelson, os models de jogador e histórico estão em `app/` e os imports precisam respeitar esse namespace. A branch `Nelson` com inicial maiúscula contém só a base inicial; o desenvolvimento está em `nelson`.

Na própria branch do Thiago ainda não foram encontrados `tests/Feature/EventoPartidaTest.php` e `database/sql/06_demonstracao.sql`. A configuração do banco de testes já foi incluída na parte do Luis.

## Pendências do grupo

1. Integrar a branch do Thiago. A branch `Luis` ainda não tem as migrations oficiais de gols e cartões, seus models/factories, módulos de eventos, estatísticas e painel.
2. Corrigir jogadores: referências a `App\Clube` em models que precisam usar `App\Models\Clube`; imports de services no controller; e o teste web que chama `/transferir` enquanto a rota registrada usa `/transferencias`.
3. Corrigir partidas: o teste usa `['rodada => 5']` e uma variável inexistente para o identificador da partida; há regra `exists` apontando para a tabela `temporada_id`, em vez de `temporada`.
4. Reexecutar a suíte completa depois da integração e das correções. A ausência de `App\Models\Gol` é uma das causas atuais de erro.
5. Versionar os scripts de backup/restauração e apresentar evidências da execução. Esses arquivos e evidências não foram encontrados nesta branch.
6. Incluir o SQL original de estrutura/dados ou o backup necessário para reproduzir o banco do primeiro bimestre. Ele foi fornecido no pacote de referência, mas ainda não está versionado nesta branch.
7. Preparar o relatório PDF do grupo, com responsabilidades, metodologia, etapas e datas, e a demonstração de cada integrante.

A atualização da main foi feita por avanço direto pelo Git. A integração conectada do GitHub recusou a criação de pull request com erro de permissão (403); nenhum histórico foi reescrito.

A implementação da parte do Luis está concluída e testada. A entrega completa do grupo depende das pendências acima.
