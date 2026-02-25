# Products Module (Piloto)

Modulo piloto para iniciar a modularizacao na Fase 2.

Estrutura base criada:
- `Http/Controllers`
- `Http/Requests`
- `Services`
- `Repositories`
- `Models`
- `Database/Migrations`
- `Database/Seeders`
- `Database/Factories`
- `Jobs`
- `Console/Commands`
- `Events`
- `Listeners`
- `Resources/js/Pages`
- `Resources/views`
- `Routes`
- `Tests`

Status atual:
- Controller, Requests, Model, Repositories e DB artifacts de `Products` ja foram movidos para este modulo.
- Rotas web/api de `Products` sao carregadas a partir de `Routes/`.
- Paginas Vue de `Products` estao co-localizadas em `Resources/js/Pages/Products`.
- Wrappers de compatibilidade permanecem em `App/*`, `database/*` e `resources/js/Pages/Products/*` durante a transicao.

Padrao do modulo:
- `Service` deve ser o ponto central de regra de negocio (a consolidar nas proximas iteracoes).
- `Repository + RepositoryEloquent` sao mantidos neste modulo por conta de datatable/busca/consultas reutilizaveis.
