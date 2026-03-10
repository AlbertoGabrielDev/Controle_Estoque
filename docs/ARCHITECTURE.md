# Arquitetura do Controle de Estoque

O projeto foi construído utilizando o padrão **Feature Modules** (Módulos por Funcionalidade). Essa abordagem foi escolhida para evitar que o escopo principal do Laravel (a pasta `app/`) se tornasse um monolito difícil de manter à medida que o sistema cresce.

## 🧱 O Padrão Feature Modules

Em vez de organizar o código por tipo técnico (todos os Controllers juntos, todos os Models juntos), organizamos por **Domínio de Negócio/Funcionalidade**.

A pasta raiz dos módulos é a `src/Modules/`:

```
Modules/
├── Admin/          # Controle de usuários e configurações
├── Categories/     # Regras de categorias
├── Customers/      # Clientes e contatos
├── Finance/        # Centros de Custo, Contas Contábeis e Despesas
├── Products/       # Produtos, marcas, variações
├── Purchases/      # Fluxo completo de aquisição
├── Sales/          # PDV, vendas corporativas e tabelas de preço
├── Settings/       # Configurações gerais da aplicação
├── Stock/          # Entradas, saídas e alertas de estoque
├── Suppliers/      # Fornecedores
└── ...
```

### O que tem dentro de cada Módulo?

Cada módulo é como um mini-sistema Laravel autocontido. Ele possui sua própria estrutura completa:

```
Modules/Finance/
├── Database/
│   ├── Migrations/      # Criação das tabelas financeiras
│   ├── Seeders/         # Dados iniciais financeiros
│   └── Factories/       # Factories para testes financeiros
├── Http/
│   ├── Controllers/     # Recebem requests financeiros e devolvem responses
│   └── Requests/        # FormRequests para validação (ex: CreateExpenseRequest)
├── Models/              # Modelos Eloquent relacionados a finanças
├── Repositories/        # Camada de acesso a dados
│   ├── Contracts/       # Interfaces (ex: ExpenseRepository)
│   └── Eloquent/        # Implementações (ex: ExpenseRepositoryEloquent)
├── Resources/
│   └── js/Pages/        # Componentes Vue.js/Inertia.js específicos deste módulo
├── Routes/              # Rotas web.php ou api.php exclusivas do módulo
├── Services/            # Onde reside a verdadeira regra de negócio financeira
└── module.json          # Metadados do módulo
```

### Como os módulos são carregados?

Existe um Service Provider na aplicação principal (`app/Providers/ModuleServiceProvider`) que varre a pasta `Modules/` e registra automaticamente as rotas, migrations e views de cada módulo ativo.

## 🔁 Fluxo de Dados (Data Flow)

Quando um usuário faz uma requisição (ex: Cadastrar Produto), o fluxo de dados segue uma direção estrita (Unidirectional Flow):

1. **Rota:** O request entra por `Modules/Products/Routes/web.php`.
2. **FormRequest:** É interceptado por um `ProductRequest` que valida se todos os campos vieram corretos.
3. **Controller:** O `ProductController` recebe os dados validados. Ele *não* contém regra de negócio. Ele apenas chama o Service.
4. **Service:** O `ProductService` recebe os dados. Aqui acontece a magia: verificar limites, calcular preços, disparar eventos, etc.
5. **Repository:** Quando o Service precisa salvar ou ler do banco, ele não chama o Model direto. Ele chama o `ProductRepository`.
6. **Model/DB:** O Repository executa a query Eloquent via Model e retorna os dados.
7. **Retorno:** A rota de volta é a Controller devolvendo uma resposta via Inertia.js (Renderização Reactiva no Vue 3).

## 🛠 Escolhas Tecnológicas e Resolução de Problemas

- **Inertia.js over API REST:** Escolhemos o Inertia para unir a robustez do Laravel com a reatividade do Vue 3 sem precisar construir e gerenciar uma API REST completa apenas para o frontend interno. Isso acelerou muito o desenvolvimento.
- **Repository Pattern:** Facilita a criação de *fakes* e *mocks* completos nos testes unitários sem tocar no banco de dados.
- **Docker:** O projeto é dockerizado desde o dia zero para garantir que o ambiente *Dev*, *Test* e *Prod* sejam absolutamente idênticos.
