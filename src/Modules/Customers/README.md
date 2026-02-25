# Customers Module

Modulo de Clientes iniciado na Fase 4.

Status inicial desta etapa:
- `Clientes`: Controller, Requests, Service, Repository, RepositoryEloquent e Model migrados para o modulo.
- `Segmentos`: Controller, Request, Service e Model migrados para o modulo.
- Rotas web e paginas Vue de `Clients` e `Segments` co-localizadas no modulo.
- DB artifacts de `Clientes` e `Segmentos` (`migrations`, `seeders`) migrados para `Database/*`.
- Wrappers de compatibilidade permanecem em `App/*`, `database/*` e `resources/js/Pages/Clients/*`.

Proximas iteracoes:
- Adicionar teste de fluxo CRUD/autocomplete de clientes e segmentos (alem dos smokes de cutover).
