#!/usr/bin/env bash
set -euo pipefail
: "${PGHOST:?Defina PGHOST}" "${PGDATABASE:?Defina PGDATABASE}" "${PGUSER:?Defina PGUSER}"
base=$(cd -- "$(dirname -- "$0")" && pwd)
pasta=${1:-"$base/../backups"}
mkdir -p "$pasta"
prefixo="$pasta/brasileirao_$(date +%Y-%m-%d_%H%M%S)"
pg_dump --schema=nelson_brasileirao_db --no-owner --no-privileges --format=custom --file="$prefixo.dump"
pg_dump --schema=nelson_brasileirao_db --no-owner --no-privileges --format=plain --file="$prefixo.sql"
psql -X -v ON_ERROR_STOP=1 -f "$base/conferir_backup.sql" > "$prefixo.txt"
echo "Backup concluído: $prefixo.dump"
