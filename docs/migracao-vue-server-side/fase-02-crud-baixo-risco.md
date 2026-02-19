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
- [x] `Pages/<Modulo>/Index.vue` com DataTable server-side.
- [x] `Pages/<Modulo>/Create.vue`.
- [x] `Pages/<Modulo>/Edit.vue`.
- [x] `Pages/<Modulo>/<Modulo>Form.vue` (quando aplicavel).
- [x] Controller migrado para `Inertia::render`.
- [x] Endpoint `*.data` implementado com `DataTableService`.
- [x] Coluna `acoes` com `EditButton` + `ButtonStatus` (exceto `role`, que nao possui status).

## Ajustes de Backend por Modulo

### Marca / Unidades / Categorias
- [x] Adicionar `HasDatatableConfig` e `dtColumns/dtFilters` nos modelos.
- [x] Ajustar controllers para fluxo Inertia.

### Fornecedor
- [x] Levar busca/filtros da Index para querystring server-side.
- [x] Validar edicao de telefones sem perda de comportamento.

### Usuario
- [x] Migrar upload de foto para fluxo Inertia (`multipart/form-data`).
- [x] Preservar associacao de roles/unidades.

### Role
- [x] Migrar matriz de permissoes para Vue mantendo logica de persistencia.
- [x] Revisar UX de toggles de permissao global/status.

## Criterio de Saida
- Todos os modulos desta fase sem uso de Blade nas telas principais.
- Listagens desses modulos rodando via `DataTable.vue` server-side.
- Fluxo Create/Edit funcionando com validacao backend atual.

## Testes PHPUnit
- [x] Criar suite dedicada da fase 2 com grupo `phase2` em:
  - `src/tests/Unit/Phase2DatatableContractsTest.php`
  - `src/tests/Feature/Phase2InertiaComponentsTest.php`
- [x] Definir comando de execucao isolada da fase 2:
  - `docker compose exec -T app sh -lc 'cd /var/www/html && php vendor/bin/phpunit --group phase2'`
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas aos CRUDs migrados nesta fase.
- [ ] Registrar resultado da execucao em `registro-andamento.md`.
