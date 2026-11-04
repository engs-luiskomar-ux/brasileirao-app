param(
    [string]$PgBin = 'C:\Program Files\PostgreSQL\18\bin',
    [string]$Pasta = (Join-Path $PSScriptRoot '..\backups')
)
$ErrorActionPreference = 'Stop'
# A conexão usa PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD e PGSSLMODE.
if (-not $env:PGHOST -or -not $env:PGDATABASE -or -not $env:PGUSER) {
    throw 'Preencha as variáveis PGHOST, PGDATABASE e PGUSER antes do backup.'
}
New-Item -ItemType Directory -Force -Path $Pasta | Out-Null
$prefixo = Join-Path $Pasta ('brasileirao_' + (Get-Date -Format 'yyyy-MM-dd_HHmmss'))
foreach ($formato in @('custom', 'plain')) {
    $extensao = if ($formato -eq 'custom') { 'dump' } else { 'sql' }
    & "$PgBin\pg_dump.exe" --schema=nelson_brasileirao_db --no-owner --no-privileges "--format=$formato" "--file=$prefixo.$extensao"
    if ($LASTEXITCODE -ne 0) { throw "Falha ao gerar o backup $extensao." }
}
& "$PgBin\psql.exe" -X -v ON_ERROR_STOP=1 -f (Join-Path $PSScriptRoot 'conferir_backup.sql') |
    Out-File -LiteralPath "$prefixo.txt" -Encoding utf8
if ($LASTEXITCODE -ne 0) { throw 'Falha ao conferir o banco.' }
Write-Output "Backup concluído: $prefixo.dump"
