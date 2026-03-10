# Technical Architecture (Controle de Estoque)

The project was built using the **Feature Modules** pattern. This approach was chosen to prevent the main Laravel scope (the `app/` folder) from becoming a monolithic mess that is hard to maintain as the system grows.

## 🧱 The Feature Modules Pattern

Instead of organizing code by technical type (all Controllers together, all Models together), we organize by **Business Domain/Feature**.

The root folder for modules is `src/Modules/`:

```
Modules/
├── Admin/          # User control and settings
├── Categories/     # Category rules
├── Customers/      # Customers and contacts
├── Finance/        # Cost Centers, Accounting Accounts and Expenses
├── Products/       # Products, brands, variations
├── Purchases/      # Complete acquisition flow
├── Sales/          # POS, corporate sales and price tables
├── Settings/       # General application settings
├── Stock/          # Stock entries, exits and alerts
├── Suppliers/      # Suppliers
└── ...
```

### What's inside each Module?

Each module is like a self-contained mini-Laravel system. It has its own complete structure:

```
Modules/Finance/
├── Database/
│   ├── Migrations/      # Financial tables creation
│   ├── Seeders/         # Initial financial data
│   └── Factories/       # Factories for financial tests
├── Http/
│   ├── Controllers/     # Receive financial requests and return responses
│   └── Requests/        # FormRequests for validation (e.g., CreateExpenseRequest)
├── Models/              # Eloquent models related to finance
├── Repositories/        # Data access layer
│   ├── Contracts/       # Interfaces (e.g., ExpenseRepository)
│   └── Eloquent/        # Implementations (e.g., ExpenseRepositoryEloquent)
├── Resources/
│   └── js/Pages/        # Vue.js/Inertia.js components specific to this module
├── Routes/              # web.php or api.php routes exclusive to the module
├── Services/            # Where true financial business logic resides
└── module.json          # Module metadata
```

### How are modules loaded?

There is a Service Provider in the main application (`app/Providers/ModuleServiceProvider`) that scans the `Modules/` folder and automatically registers the routes, migrations, and views of each active module.

## 🔁 Unidirectional Data Flow

When a user makes a request (e.g., Register Product), the data flow follows a strict direction:

1. **Route:** The request enters through `Modules/Products/Routes/web.php`.
2. **FormRequest:** It is intercepted by a `ProductRequest` validating if all fields are correct.
3. **Controller:** The `ProductController` receives the validated data. It *does not* contain business logic. It simply calls the Service.
4. **Service:** The `ProductService` receives the data. Here is where the magic happens: checking limits, calculating prices, dispatching events, etc.
5. **Repository:** When the Service needs to read or write directly to the database, it doesn't call the Model strictly. It calls the `ProductRepository`.
6. **Model/DB:** The Repository executes the Eloquent query via Model and returns the data.
7. **Return:** The route back is the Controller returning a response via Inertia.js (Reactive Rendering in Vue 3).

## 🛠 Technological Choices

- **Inertia.js over REST API:** We chose Inertia to blend Laravel's robustness with Vue 3's reactivity without the need to build and manage a complete REST API just for the internal frontend. This massively accelerated development.
- **Repository Pattern:** Facilitates the creation of complete *fakes* and *mocks* in unit tests without touching the database.
- **Docker:** The project is dockerized from day zero to ensure the *Dev*, *Test*, and *Prod* environments are absolutely identical.
