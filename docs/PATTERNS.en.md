# Design Patterns

To maintain the consistency and testability of the **Controle de Estoque**, we adopted a strict set of design patterns. This ensures that a developer understands the entire codebase after reading just a single module.

## 1. Repository Pattern

We use the Repository pattern via the `prettus/l5-repository` package.

**Why do we use it?**
To abstract complex database queries and avoid spreading direct Eloquent Function calls (`Model::where(...)`) throughout the code (Controllers and Services). This allows us to:
- Change the ORM or database in the future (unlikely, but possible).
- Use Mocks for repositories in unit tests extremely easily and quickly.
- Centralize complex query scopes (e.g., fetching a product only if active, alongside supplier relation and filtered by color).

**Standard Format:**
Every "important" entity has an interface (`Contract`) and a concrete implementation (`Eloquent`).

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

Laravel's dependency injection container (AppServiceProvider) binds the Interface and the Implementation together.

## 2. Service Layer Pattern

The Service Layer is the beating heart of the project.

**Why do we use it?**
A Controller should never know "how" to do something, only "what" needs to be done. If we spread business rules inside controllers (complex `if/else`, multiple record creations, email dispatching), the code becomes impossible to reuse (e.g., in a Job or terminal Command).

**Standard Format:**

```php
// Modules/Stock/Services/StockService.php
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
            // Rule 1: Register movement
            // Rule 2: Update current balance
            // Rule 3: Trigger alert if below minimum
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

## 3. FormRequests for Input Validation

**Why do we use it?**
No validation (`$request->validate()`) is directly done inside the Controller. We use Laravel's native `FormRequest` classes to encapsulate security and validation rules, cleaning up the Controller logic.

## 4. Inertia.js Page Objects (Frontend Pattern)

In the frontend with Vue 3, we utilize purely visual components and pass heavy data (Props) directly from the Controller through Inertia.

We don't have initial `axios.get('/api/products')` calls on the screen load. Laravel sends the initial HTML + JSON at the exact moment of rendering via:

```php
return Inertia::render('Products/Index', [
    'products' => $productsPaginator
]);
```
This unites the worlds of the Classic MPA Monolith and the modern SPA.
