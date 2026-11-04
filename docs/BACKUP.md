# Backup e restauração

Use os programas do PostgreSQL 18. Os scripts recebem a conexão pelas variáveis `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD` e `PGSSLMODE`. Elas correspondem aos dados `DB_*` do `.env`. Não coloque senhas nos arquivos do projeto.

## Backup do Neon

Depois de preencher essas variáveis com os dados do Neon e `PGSSLMODE=require`, rode no PowerShell:

```powershell
.\scripts\backup_neon.ps1
```

No Linux/Git Bash, com os programas do PostgreSQL no PATH:

```bash
bash scripts/backup_neon.sh
```

São gerados um `.dump`, um `.sql` e uma conferência de tabelas e objetos em `backups/`. Essa pasta fica fora do Git. O backup só lê o Neon.

## Restauração em banco separado

Os scripts de restauração aceitam apenas um banco local chamado `brasileirao_restore`, sem o schema do projeto. Não apagam um schema existente.

No PowerShell, mantenha a porta e as credenciais do seu PostgreSQL local:

```powershell
$env:PGHOST = '127.0.0.1'
$env:PGPORT = '5432'
$env:PGUSER = 'postgres'
$env:PGDATABASE = 'brasileirao_restore'
$env:PGSSLMODE = 'prefer'
# Ajuste PGPASSWORD para a senha local antes de continuar.
& 'C:\Program Files\PostgreSQL\18\bin\createdb.exe' brasileirao_restore
.\scripts\restaurar_backup.ps1 -Arquivo '.\backups\nome_do_backup.dump'
```

No Linux/Git Bash, configure as mesmas variáveis e use `createdb brasileirao_restore` e `bash scripts/restaurar_backup.sh backups/nome_do_backup.dump`.

Os scripts param se houver erro e mostram as contagens, functions, procedures, triggers e índices restaurados. Compare com o arquivo `.txt` do backup. Faça o backup sem cadastros ou edições simultâneas para comparar as contagens do mesmo estado.

## Dados originais do 1º bimestre

`database/sql/00_backup_original_nelson.sql` veio do pacote fornecido pelo grupo. Foram omitidas duas instruções de permissões internas do Neon (`cloud_admin` / `neon_superuser`) para permitir a restauração local. A estrutura e os dados foram mantidos.

Para usar esse SQL, crie outro banco vazio local, selecione esse banco nas variáveis `PG*` e importe com:

```bash
psql -X --single-transaction -v ON_ERROR_STOP=1 -f database/sql/00_backup_original_nelson.sql
```

Depois, aponte o `.env` da aplicação para esse banco local e rode `php artisan migrate` para acrescentar os objetos e adequações do 2º bimestre. Não importe o SQL sobre o banco do grupo.
