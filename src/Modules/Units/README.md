# Units Module

Modulo de Unidades (filiais) iniciado na Fase 4.

Status inicial desta etapa:
- Controller, Requests, Service e Model migrados para o modulo.
- Rotas web e paginas Vue de `Units` co-localizadas no modulo.
- DB artifacts (`migrations`, `seeders`) migrados para `Database/*`.
- Wrappers de compatibilidade permanecem em `App/*`, `database/*` e `resources/js/Pages/Units/*`.
- `UnidadesRepository`/`UnidadesRepositoryEloquent` legados em `App/Repositories` permanecem temporariamente (fora do padrao alvo do modulo nesta fase).

Proximas iteracoes:
- Adicionar teste de fluxo CRUD basico (alem do smoke de cutover).
