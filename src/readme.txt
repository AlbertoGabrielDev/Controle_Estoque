npm i -D vite @vitejs/plugin-vue laravel-vite-plugin
npm i vue@^3.4 @inertiajs/vue3 axios
npm i jquery

CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- cria/ajusta o usuário para qualquer origem da rede Docker
CREATE USER IF NOT EXISTS 'laravel'@'%' IDENTIFIED BY 'toor';
ALTER USER 'laravel'@'%' IDENTIFIED WITH mysql_native_password BY 'toor';

-- dá permissão no banco
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%';
FLUSH PRIVILEGES;