# Patrones de Diseño del Proyecto

Para mantener la consistencia y la facilidad de prueba del **Controle de Estoque**, adoptamos un conjunto estricto de patrones de diseño (Design Patterns). Esto garantiza que un desarrollador entienda toda la base de código tras leer apenas un módulo.

## 1. Repository Pattern

Utilizamos el patrón Repository vía el paquete `prettus/l5-repository`.

**¿Por qué lo usamos?**
Para abstraer consultas (queries) complejas a la base de datos y evitar esparcir el uso directo de Funciones Eloquent (`Model::where(...)`) por todo el código (Controllers y Services). Esto permite:
- Cambiar el ORM o la base de datos en el futuro (improbable, pero posible).
- Usar Mocks de los repositorios en las pruebas unitarias de forma extremadamente simple y rápida.
- Centralizar scopes de consultas complicadas (ej: traer un producto solo si está activo, con la relación del proveedor y filtrado por color).

**Formato estándar:**
Toda entidad "importante" tiene una interfaz (`Contract`) y una implementación concreta (`Eloquent`).

```php
// Modules/Finance/Repositories/Contracts/ExpenseRepository.php
interface ExpenseRepository extends RepositoryInterface {
    public function getPendingExpensesByPeriod($start, $end);
}

// Modules/Finance/Repositories/Eloquent/ExpenseRepositoryEloquent.php
class ExpenseRepositoryEloquent extends BaseRepository implements ExpenseRepository {
    public function model() { return Expense::class; }
    
    public function getPendingExpensesByPeriod($start, $end) {
        return $this->model->whereBetween('due_date', [$start, $end])
                           ->where('status', 'pending')
                           ->get();
    }
}
```

El contenedor de inyección de dependencias de Laravel (AppServiceProvider) realiza el bind (enlace) entre la Interfaz y la Implementación.

## 2. Service Layer Pattern

La Capa de Servicio (Service Layer) es el corazón palpitante del proyecto.

**¿Por qué lo usamos?**
El Controller nunca debe saber "cómo" hacer algo, solo "qué" debe hacerse. Si esparcimos reglas de negocio dentro de los controllers (complejos `if/else`, creación de múltiples registros, envío de emails), el código se vuelve imposible de reutilizar (ej: en un Job o en un Comando de terminal).

**Formato estándar:**

```php
// Moduels/Stock/Services/StockService.php
class StockService
{
    private $stockRepository;

    public function __construct(StockRepository $repository) {
        $this->stockRepository = $repository;
    }

    public function adjustStock(array $data)
    {
        DB::beginTransaction();
        try {
            // Regla 1: Registrar movimiento
            // Regla 2: Actualizar saldo actual
            // Regla 3: Disparar alerta si cae debajo del mínimo
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

## 3. FormRequests para Validación de Entrada

**¿Por qué lo usamos?**
Ninguna validación (`$request->validate()`) se hace directamente dentro del Controller. Usamos las clases `FormRequest` nativas de Laravel para encapsular reglas de seguridad y validación, limpiando la lógica del Controller.

## 4. Inertia.js Page Objects (Patrón Frontend)

En el frontend con Vue 3, utilizamos componentes puramente visuales y pasamos los datos pesados (Props) directamente del Controller por medio de Inertia. 

No realizamos llamadas iniciales estilo `axios.get('/api/produtos')` en la carga de la pantalla. Laravel envía el HTML inicial + JSON en el momento exacto del render vía:

```php
return Inertia::render('Products/Index', [
    'products' => $productsPaginator
]);
```
Esto une los mundos de la aplicación monolítica clásica (MPA) y la aplicación moderna (SPA).
