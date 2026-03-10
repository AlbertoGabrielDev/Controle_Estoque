# Design Patterns do Projeto

Para manter a consistência e testabilidade do **Controle de Estoque**, adotamos um conjunto rigoroso de padrões de projeto (Design Patterns). Isso garante que um desenvolvedor entenda toda a base de código após ler apenas um módulo.

## 1. Repository Pattern

Utilizamos o padrão Repository via pacote `prettus/l5-repository`.

**Por que usamos?**
Para abstrair as consultas (queries) complexas do banco de dados e evitar espalhar o uso direto de Eloquent Functions (`Model::where(...)`) por todo o código (Controllers e Services). Isso permite:
- Trocar o ORM ou banco no futuro (improvável, mas possível).
- Usar Mocks dos repositórios nos testes unitários de forma extremante simples e rápida.
- Centralizar scopes de queries complicadas (ex: trazer produto apenas se ativo, com relação de fornecedor e filtrado por cor).

**Formato padrão:**
Toda entidade "importante" tem uma interface (`Contract`) e uma implementação concreta (`Eloquent`).

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

O contêiner de injeção de dependência do Laravel (AppServiceProvider) faz o bind entre a Interface e a Implementação.

## 2. Service Layer Pattern

A camada de Serviço (Service Layer) é o coração pulsante do projeto.

**Por que usamos?**
O Controller nunca deve saber "como" fazer algo, apenas "o que" deve ser feito. Se espalharmos regras de negócio nos controllers (`if/else` complexos, criação de múltiplos registros, disparos de email), o código fica impossível de reutilizar (ex: em um Job ou em um Command de terminal).

**Formato padrão:**

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
            // Regra 1: Registrar movimento
            // Regra 2: Atualizar saldo atual
            // Regra 3: Disparar alerta se ficar abaixo do mínimo
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

## 3. FormRequests para Validação de Entrada

**Por que usamos?**
Nenhuma validação (`$request->validate()`) é feita diretamente dentro do Controller. Usamos as classes `FormRequest` nativas do Laravel para encapsular as regras de segurança e validação, limpando a lógica do Controller.

## 4. Inertia.js Page Objects (Padrão Frontend)

No frontend com Vue 3, utilizamos componentes puramente visuais e passamos os dados pesados (Props) direto do Controller pelo Inertia. 

Não temos chamadas `axios.get('/api/produtos')` na carga inicial da tela. O Laravel envia o HTML + JSON inicial no momento exato do render via:

```php
return Inertia::render('Products/Index', [
    'products' => $productsPaginator
]);
```
Isso une os mundos do Monolito Clássico MPA e do SPA moderno.
