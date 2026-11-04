# Apresentação (10 minutos)

1. **Luis (0–3 min):** explicar o tema e a escolha do banco do Nelson; abrir clubes, temporadas e classificação. Mostrar um cadastro de clube com UF minúscula e a normalização pela trigger. Explicar a function da classificação e a procedure de temporada.
2. **Nelson (3–5 min):** abrir um jogador e seu histórico; demonstrar a transferência e a validação da camisa. Mostrar as evidências do backup e da restauração em banco local separado.
3. **João (5–7 min):** cadastrar uma partida e abrir a súmula; mostrar o placar calculado e a recusa de um clube que já joga naquela rodada. Explicar procedure e trigger.
4. **Thiago (7–9 min):** registrar gol e cartões; demonstrar o segundo amarelo gerando vermelho e a artilharia. Explicar gol contra e bloqueio de gol depois da expulsão.
5. **Grupo (9–10 min):** mostrar a API de clubes em JSON, os resultados dos testes, o Git e a divisão dos módulos.

Antes de apresentar, rodar `php artisan db:verificar` e `php artisan migrate`. Preparar os dados de demonstração com antecedência em uma cópia local; não restaurar backups sobre o Neon compartilhado. O `.env` não deve aparecer na apresentação.

Cada integrante deve saber explicar seu controller, request, service, model, migration e objeto SQL. Todos devem entender MVC, JSON e códigos HTTP, function x procedure, trigger BEFORE/AFTER e o motivo dos índices.
