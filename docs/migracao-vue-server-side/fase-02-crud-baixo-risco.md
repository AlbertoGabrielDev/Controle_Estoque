# Fase 02 - Migracao de CRUD Baixo Risco

## Objetivo
Migrar modulos CRUD mais simples para validar o processo completo com baixo risco de negocio.

## Modulos desta fase
- `marca`
- `unidades`
- `categorias`
- `fornecedor`
- `role`
- `usuario`

## Padrao de Entrega por Modulo
- [ ] `Pages/<Modulo>/Index.vue` com DataTable server-side.
- [ ] `Pages/<Modulo>/Create.vue`.
- [ ] `Pages/<Modulo>/Edit.vue`.
- [ ] `Pages/<Modulo>/<Modulo>Form.vue` (quando aplicavel).
- [ ] Controller migrado para `Inertia::render`.
- [ ] Endpoint `*.data` implementado com `DataTableService`.
- [ ] Coluna `acoes` com `EditButton` + `ButtonStatus`.

## Ajustes de Backend por Modulo

### Marca / Unidades / Categorias
- [ ] Adicionar `HasDatatableConfig` e `dtColumns/dtFilters` nos modelos.
- [ ] Ajustar controllers para fluxo Inertia.

### Fornecedor
- [ ] Levar busca/filtros da Index para querystring server-side.
- [ ] Validar edicao de telefones sem perda de comportamento.

### Usuario
- [ ] Migrar upload de foto para fluxo Inertia (`multipart/form-data`).
- [ ] Preservar associacao de roles/unidades.

### Role
- [ ] Migrar matriz de permissoes para Vue mantendo logica de persistencia.
- [ ] Revisar UX de toggles de permissao global/status.

## Criterio de Saida
- Todos os modulos desta fase sem uso de Blade nas telas principais.
- Listagens desses modulos rodando via `DataTable.vue` server-side.
- Fluxo Create/Edit funcionando com validacao backend atual.

## Testes PHPUnit
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas aos CRUDs migrados nesta fase.
- [ ] Registrar resultado da execucao em `registro-andamento.md`.
