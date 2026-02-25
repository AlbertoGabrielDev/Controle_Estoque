# Suppliers Module

Modulo de Fornecedores iniciado na Fase 4.

Status inicial desta etapa:
- Controller, Requests, Service e Model migrados para o modulo.
- Rotas web e paginas Vue de `Suppliers` co-localizadas no modulo.
- DB artifacts (`migrations`, `seeders`, `factories`) migrados para `Database/*`.
- Wrappers de compatibilidade permanecem em `App/*`, `database/*` e `resources/js/Pages/Suppliers/*`.
- `Telefone` permanece compartilhado em `App/Models` nesta etapa.

Proximas iteracoes:
- Adicionar teste de fluxo CRUD basico (alem do smoke de cutover).
