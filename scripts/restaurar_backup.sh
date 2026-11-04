#!/usr/bin/env bash
set -euo pipefail
arquivo=${1:?Informe o arquivo .dump}
case "${PGHOST:-}" in 127.0.0.1|localhost|::1) ;; *) echo 'Use PGHOST local.' >&2; exit 1 ;; esac
[[ "${PGDATABASE:-}" == brasileirao_restore ]] || { echo 'Use PGDATABASE=brasileirao_restore.' >&2; exit 1; }
[[ -f "$arquivo" ]] || { echo 'Backup não encontrado.' >&2; exit 1; }
destino=$(psql -X -A -t -v ON_ERROR_STOP=1 -c "SELECT current_database() || '|' || (inet_server_addr() IN ('127.0.0.1'::inet, '::1'::inet))::text || '|' || (to_regnamespace('nelson_brasileirao_db') IS NULL)::text")
[[ "$destino" == 'brasileirao_restore|true|true' ]] || { echo 'O destino precisa ser local, separado e sem o schema do projeto.' >&2; exit 1; }
pg_restore --no-owner --no-privileges --single-transaction --exit-on-error --dbname="$PGDATABASE" "$arquivo"
base=$(cd -- "$(dirname -- "$0")" && pwd)
psql -X -v ON_ERROR_STOP=1 -f "$base/conferir_backup.sql"
echo 'Restauração concluída no banco local brasileirao_restore.'
