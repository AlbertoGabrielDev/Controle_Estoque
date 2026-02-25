# Categories Module

Modulo de Categorias iniciado na Fase 4.

Status inicial desta etapa:
- Controller, Requests, Service e Model migrados para o modulo.
- Rotas web e paginas Vue de `Categories` co-localizadas no modulo.
- DB artifacts (`migrations`, `seeders`, `factories`) migrados para `Database/*`.
- Wrappers de compatibilidade permanecem em `App/*`, `database/*` e `resources/js/Pages/Categories/*`.
- Repositories legados de `Categoria` em `App/Repositories` permanecem temporariamente (nao fazem parte do padrao alvo do modulo nesta fase).

Proximas iteracoes:
- Adicionar teste de fluxo CRUD basico (alem do smoke de cutover).
