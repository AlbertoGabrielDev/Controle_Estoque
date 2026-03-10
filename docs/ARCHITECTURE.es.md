# Arquitectura Técnica (Controle de Estoque)

El proyecto se construyó utilizando el patrón de **Módulos por Funcionalidad (Feature Modules)**. Este enfoque se eligió para evitar que el alcance principal de Laravel (la carpeta `app/`) se convirtiera en un monolito difícil de mantener a medida que el sistema creciese.

## 🧱 El Patrón de Módulos (Feature Modules)

En lugar de organizar el código por tipo técnico (todos los Controllers juntos, todos los Models juntos), lo organizamos por **Dominio de Negocio/Funcionalidad**.

La carpeta principal de los módulos es `src/Modules/`:

```
Modules/
├── Admin/          # Control de usuarios y configuraciones
├── Categories/     # Reglas de categorías
├── Customers/      # Clientes y contactos
├── Finance/        # Centros de Costos, Cuentas Contables y Egresos
├── Products/       # Productos, marcas, variaciones
├── Purchases/      # Flujo completo de adquisición
├── Sales/          # PDV, ventas corporativas y tablas de precios
├── Settings/       # Configuraciones generales de la aplicación
├── Stock/          # Entradas, salidas y alertas de stock
├── Suppliers/      # Proveedores
└── ...
```

### ¿Qué hay dentro de cada Módulo?

Cada módulo es como un mini-sistema Laravel autocontenido. Tiene su propia estructura completa:

```
Modules/Finance/
├── Database/
│   ├── Migrations/      # Creación de tablas financieras
│   ├── Seeders/         # Datos iniciales financieros
│   └── Factories/       # Factories para pruebas financieras
├── Http/
│   ├── Controllers/     # Reciben solicitudes financieras y devuelven respuestas
│   └── Requests/        # FormRequests para validación (ej: CreateExpenseRequest)
├── Models/              # Modelos Eloquent relacionados a finanzas
├── Repositories/        # Capa de acceso a datos
│   ├── Contracts/       # Interfaces (ej: ExpenseRepository)
│   └── Eloquent/        # Implementaciones (ej: ExpenseRepositoryEloquent)
├── Resources/
│   └── js/Pages/        # Componentes Vue.js/Inertia.js específicos de este módulo
├── Routes/              # Rutas web.php o api.php exclusivas del módulo
├── Services/            # Donde reside la verdadera regla de negocio financiera
└── module.json          # Metadatos del módulo
```

### ¿Cómo se cargan los módulos?

Existe un Service Provider en la aplicación principal (`app/Providers/ModuleServiceProvider`) que barre la carpeta `Modules/` y registra automáticamente las rutas, migraciones y vistas de cada módulo activo.

## 🔁 Flujo de Datos (Data Flow)

Cuando un usuario hace una petición (ej: Registrar Producto), el flujo de datos sigue una dirección estricta (Unidirectional Flow):

1. **Ruta:** La solicitud entra por `Modules/Products/Routes/web.php`.
2. **FormRequest:** Es interceptado por un `ProductRequest` que valida si todos los campos son correctos.
3. **Controller:** El `ProductController` recibe los datos validados. Este *no* contiene reglas de negocio. Solamente llama al Service.
4. **Service:** El `ProductService` recibe los datos. Aquí ocurre la magia: verificar límites, calcular precios, despachar eventos, etc.
5. **Repository:** Cuando el Service necesita guardar o leer de la base de datos, no llama al Model directamente. Llama al `ProductRepository`.
6. **Model/DB:** El Repository ejecuta la consulta Eloquent vía Model y retorna los datos.
7. **Retorno:** La ruta de regreso es la Controller devolviendo una respuesta vía Inertia.js (Renderizado Reactivo en Vue 3).

## 🛠 Decisiones Tecnológicas y Resolución de Problemas

- **Inertia.js over API REST:** Elegimos Inertia para unir la robustez de Laravel con la reactividad de Vue 3 sin tener que construir y gestionar una API REST completa solamente para el frontend interno. Esto aceleró muchísimo el desarrollo.
- **Repository Pattern:** Facilita la creación de *fakes* y *mocks* completos en las pruebas unitarias sin tocar en absoluto la base de datos.
- **Docker:** El proyecto está dockerizado desde el día cero para garantizar que el ambiente de *Dev*, *Test* y *Prod* sean absolutamente idénticos.
