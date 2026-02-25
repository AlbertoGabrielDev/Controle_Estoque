# PriceTables Module

Modulo de Tabelas de Preco iniciado na Fase 3.

Status atual da etapa:
- Controller, Requests, Service, Repository e Model migrados para o modulo.
- Rotas web e paginas Vue de `PriceTables` co-localizadas no modulo.
- DB artifacts (`migrations`, `seeders`) migrados para `Database/*` no modulo.
- Padrao `Repository (contrato) + RepositoryEloquent` aplicado.
- Wrappers de compatibilidade permanecem em `App/*` e `resources/js/Pages/PriceTables/*`.

Proximas iteracoes:
- Adicionar testes de fluxo mais completos para CRUD/itens de tabela de preco (alem do smoke atual).
