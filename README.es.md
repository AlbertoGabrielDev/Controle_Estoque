<h1 align="center">
  🏭 Software de Inventario (Controle de Estoque)
</h1>

<p align="center">
  <em>Leer en otros idiomas:</em><br>
  🇧🇷 <a href="./README.md">Português</a> &nbsp;&middot;&nbsp; 🇺🇸 <a href="./README.en.md">English</a> &nbsp;&middot;&nbsp; 🇪🇸 <strong>Español</strong>
</p>

<p align="center">
  Sistema modular para la gestión de inventario, compras, finanzas y ventas,<br>
  construido con <strong>Laravel 10 · Vue.js · Inertia.js · Docker</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.1"/>
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 10"/>
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Vue 3"/>
  <img src="https://img.shields.io/badge/Inertia.js-purple?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js"/>
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker"/>
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"/>
  <img src="https://img.shields.io/badge/PHPUnit-10-6C9CCD?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit"/>
</p>

<p align="center">
  <a href="#-acerca-del-proyecto">Acerca de</a> •
  <a href="#-características">Características</a> •
  <a href="#-tecnologías">Tecnologías</a> •
  <a href="#-arquitectura">Arquitectura</a> •
  <a href="#-cómo-ejecutar">Cómo Ejecutar</a> •
  <a href="#-pruebas">Pruebas</a>
</p>

---

## 📌 Acerca del Proyecto

El **Sistema de Control de Inventario** es una aplicación web completa diseñada para pequeñas y medianas empresas que necesitan centralizar la gestión de su **inventario, compras, proveedores, finanzas y ventas** en un solo lugar.

El sistema ofrece **alertas visuales inteligentes**: los productos por debajo de la cantidad mínima de stock resaltan en **morado**, los productos caducados se vuelven **rojos** y los productos próximos a caducar (en menos de 7 días) se vuelven **amarillos** — asegurando que el gerente nunca pierda el control.

El proyecto está desarrollado con un enfoque en la **arquitectura limpia**, utilizando el patrón de **Módulos por Funcionalidad (Feature Modules)**, el **Patrón Repositorio (Repository Pattern)**, la **Capa de Servicio (Service Layer)** y **FormRequests** para una clara separación de responsabilidades.

---

## ✅ Características

### 📦 Módulo de Inventario (Stock)
- Registro y control de productos en stock.
- Alertas visuales automáticas: caducidad (🔴 caducado / 🟡 a 7 días / 🟣 por debajo del mínimo).
- Sistema avanzado de filtros en todos los campos relevantes.
- Historial de salida de productos (visible solo para administradores, vía caché de Laravel).

### 🛒 Módulo de Compras (Purchases)
- Flujo de trabajo completo: Requisición → Cotización → Orden de Compra → Recepción → Conferencia → Devolución → Cuentas por Pagar.
- Control de proveedores y órdenes de compra.

### 💰 Módulo Financiero (Finance)
- Centros de Costos (con jerarquía padre/hijo).
- Cuentas Contables.
- Control de Gastos (Egresos).

### 🏷️ Módulo de Productos (Products)
- Registro de productos, marcas y categorías.
- Control de unidades de medida y tablas de precios.
- Activación/desactivación por el administrador.

### 👥 Módulo de Clientes y Proveedores (Customers & Suppliers)
- Registro completo de clientes y proveedores.
- Búsqueda por nombre en todas las listas.

### ⚙️ Módulo de Administración (Admin)
- Gestión de usuarios.
- Control de acceso por roles.
- Activación/desactivación de registros (productos, proveedores, marcas).

### 🔧 Características Transversales
- Autenticación con **Laravel Jetstream + Sanctum**.
- Exportación a **Excel** (maatwebsite/excel).
- **DataTables** con paginación desde el servidor (server-side).
- Integración con la **API de Google** (google/apiclient).
- Bot de **WhatsApp** integrado.
- Contenedores con **Docker + docker-compose**.

---

## 🛠️ Tecnologías

| Capa | Tecnologías |
|--------|------------|
| **Backend** | PHP 8.1 · Laravel 10 · Laravel Jetstream · Sanctum · Livewire 3 |
| **Frontend** | Vue.js 3 · Inertia.js · Vite · Tailwind CSS · Bootstrap 5 |
| **Base de Datos** | MySQL 8 (vía Docker) |
| **Pruebas** | PHPUnit 10 · Pruebas de Características (Feature Tests) · Pruebas Unitarias |
| **Infraestructura**| Docker · docker-compose · Nginx |
| **Herramientas** | Laravel DataTables · Maatwebsite Excel · Ziggy · L5-Repository |

---

## 🏗️ Arquitectura

El proyecto sigue una arquitectura de **Módulos por Funcionalidad (Feature Modules)**, donde cada dominio de negocio está aislado en su propio módulo junto con todos sus artefactos.

```
Modules/
├── Admin/
├── Finance/        ← Centros de Costos, Cuentas Contables, Egresos
├── Products/       ← Productos, Marcas, Categorías
├── Purchases/      ← Flujo completo de compras
├── Sales/          ← Ventas y tablas de precios
├── Stock/          ← Inventario con alertas visuales
├── Customers/
├── Suppliers/
└── ...
```

Cada módulo se adhiere al siguiente patrón:

```
Modules/<Módulo>/
├── Http/
│   ├── Controllers/
│   └── Requests/         ← FormRequests (validación)
├── Services/             ← Lógica de negocio y orquestación
├── Repositories/
│   ├── Contracts/        ← Interfaces del repositorio
│   └── Eloquent/         ← Implementaciones de Eloquent
├── Models/               ← Relaciones, casts y scopes
├── Database/
│   ├── Migrations/
│   ├── Seeders/
│   └── Factories/
├── Resources/
│   └── js/Pages/         ← Páginas de Vue.js (Inertia)
└── Routes/
```

### Flujo de Datos de Solicitud HTTP (Data Flow)

```mermaid
flowchart LR
    HTTP["🌐 Solicitud HTTP"]
    FR["FormRequest\n(Validación)"]
    C["Controller"]
    S["Service\n(Lógica de Negocio)"]
    R["Repository\n(Interfaz)"]
    E["RepositoryEloquent\n(Implementación)"]
    DB[("MySQL")]

    HTTP --> FR --> C --> S --> R --> E --> DB
```

---

## 🚀 Cómo Ejecutar Localmente

### Requisitos Previos
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado.
- [Git](https://git-scm.com/).

### Pasos de Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/SEU_USUARIO/Controle_Estoque.git
cd Controle_Estoque

# 2. Levantar los contenedores
docker-compose up -d

# 3. Acceder al contenedor de la aplicación
docker exec -it controle_estoque_app bash

# 4. Dentro del contenedor:
cd /var/www/html

# 5. Instalar dependencias de PHP
composer install

# 6. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 7. Ejecutar migraciones y seeders
php artisan migrate --seed

# 8. Instalar dependencias JS y compilar assets
npm install
npm run build

# 9. Acceder a la aplicación en: http://localhost
```

> **Consejo:** Para desarrollo con recarga en vivo (hot-reload), use `npm run dev` en lugar de `npm run build`.

---

## 🧪 Pruebas

El proyecto tiene una suite de pruebas construida con **PHPUnit 10**, organizada por módulo:

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas para un módulo específico
php artisan test --testsuite=Modules

# Ejecutar con cobertura de código
php artisan test --coverage
```

Las pruebas cubren:
- ✅ **Pruebas de Características (Feature Tests)** — Flujos HTTP completos (rutas, controladores, respuestas).
- ✅ **Pruebas Unitarias (Unit Tests)** — Servicios aislados y reglas de negocio.

---

## 📄 Documentación Técnica

| Documento | Descripción |
|-----------|-----------|
| [Arquitectura](docs/ARCHITECTURE.es.md) | Visión general de la arquitectura (Facture Modules) |
| [Módulos](docs/MODULES.es.md) | Descripción detallada de cada módulo |
| [Patrones](docs/PATTERNS.es.md) | Padrón Repositorio, Capa de Servicio, FormRequests |
| [Contribución](CONTRIBUTING.es.md) | Guía para contribuyentes |

---

## 👨‍💻 Autor

Desarrollado por **Alberto Gabriel**

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/albertogabrieldev/)
[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/SEU_USUARIO)

---

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.
