param(
    [Parameter(Mandatory = $true)][string]$Arquivo,
    [string]$PgBin = 'C:\Program Files\PostgreSQL\18\bin'
)
$ErrorActionPreference = 'Stop'
# Restaura somente em banco local separado. Não apaga tabelas existentes.
if ($env:PGHOST -notin @('127.0.0.1', 'localhost', '::1') -or $env:PGDATABASE -ne 'brasileirao_restore') {
    throw 'Use PGHOST local e PGDATABASE=brasileirao_restore.'
}
if (-not (Test-Path -LiteralPath $Arquivo -PathType Leaf)) { throw 'Backup não encontrado.' }
$destino = & "$PgBin\psql.exe" -X -A -t -v ON_ERROR_STOP=1 -c "SELECT current_database() || '|' || (inet_server_addr() IN ('127.0.0.1'::inet, '::1'::inet))::text || '|' || (to_regnamespace('nelson_brasileirao_db') IS NULL)::text"
if ($LASTEXITCODE -ne 0 -or "$destino".Trim() -ne 'brasileirao_restore|true|true') {
    throw 'O destino precisa ser local, separado e sem o schema do projeto.'
}
& "$PgBin\pg_restore.exe" --no-owner --no-privileges --single-transaction --exit-on-error "--dbname=$env:PGDATABASE" $Arquivo
if ($LASTEXITCODE -ne 0) { throw 'Falha na restauração.' }
& "$PgBin\psql.exe" -X -v ON_ERROR_STOP=1 -f (Join-Path $PSScriptRoot 'conferir_backup.sql')
if ($LASTEXITCODE -ne 0) { throw 'Falha na conferência da restauração.' }
Write-Output 'Restauração concluída no banco local brasileirao_restore.'
