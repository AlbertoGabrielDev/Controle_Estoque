#!/bin/sh
set -e
cd /var/www/html

# --- Garantir diretórios e permissões ---
mkdir -p bootstrap/cache \
         storage/framework/{cache,data,sessions,testing,views} \
         storage/logs
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# --- Dependências ---
if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader
fi

# --- App key / .env ---
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    cp .env.example .env
fi
php artisan key:generate --force || true

# --- Limpa caches só no start ---
php artisan optimize:clear || true

# --- Espera banco (usa variáveis do .env/.compose) ---
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_WAIT_RETRIES="${DB_WAIT_RETRIES:-30}"
DB_WAIT_SLEEP="${DB_WAIT_SLEEP:-2}"

echo "Aguardando banco em ${DB_HOST}:${DB_PORT} ..."
i=0
until (echo > /dev/tcp/${DB_HOST}/${DB_PORT}) 2>/dev/null; do
  i=$((i+1))
  if [ "$i" -ge "$DB_WAIT_RETRIES" ]; then
    echo "Banco não respondeu após $((DB_WAIT_RETRIES*DB_WAIT_SLEEP))s"; break
  fi
  sleep "$DB_WAIT_SLEEP"
done

# --- Migrations/Seeds controlados por flags ---
RUN_MIGRATIONS="${RUN_MIGRATIONS:-1}"   # 1 para rodar
MIGRATE_FRESH="${MIGRATE_FRESH:-0}"     # 1 para migrate:fresh
RUN_SEED="${RUN_SEED:-0}"               # 1 para rodar seed
SEED_CLASS="${SEED_CLASS:-}"            # ex: DatabaseSeeder ou VendaSeeder
ALWAYS_RUN="${ALWAYS_RUN:-0}"           # 1 ignora arquivos-trava

MIGRATED_LOCK="storage/framework/.migrated"
SEEDED_LOCK="storage/framework/.seeded"

if [ "$RUN_MIGRATIONS" = "1" ]; then
  if [ "$ALWAYS_RUN" = "1" ] || [ ! -f "$MIGRATED_LOCK" ]; then
    if [ "$MIGRATE_FRESH" = "1" ]; then
      echo ">> php artisan migrate:fresh --seed? ($RUN_SEED)"; 
      if [ "$RUN_SEED" = "1" ] && [ -n "$SEED_CLASS" ]; then
        php artisan migrate:fresh --force --no-interaction
        php artisan db:seed --force --no-interaction --class="$SEED_CLASS"
      elif [ "$RUN_SEED" = "1" ]; then
        php artisan migrate:fresh --seed --force --no-interaction
      else
        php artisan migrate:fresh --force --no-interaction
      fi
    else
      echo ">> php artisan migrate"
      php artisan migrate --force --no-interaction
      if [ "$RUN_SEED" = "1" ]; then
        echo ">> php artisan db:seed"
        if [ -n "$SEED_CLASS" ]; then
          php artisan db:seed --force --no-interaction --class="$SEED_CLASS"
        else
          php artisan db:seed --force --no-interaction
        fi
      fi
    fi
    touch "$MIGRATED_LOCK"
    [ "$RUN_SEED" = "1" ] && touch "$SEEDED_LOCK"
  else
    echo "Migrations já executadas (lock: $MIGRATED_LOCK). Pulei."
  fi
else
  echo "RUN_MIGRATIONS=0 → não vou migrar."
fi

# --- Permissões finais (volumes podem “zerar” na primeira subida) ---
chown -R www-data:www-data storage bootstrap/cache vendor || true
chmod -R 775 storage/bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache || true

exec "$@"
