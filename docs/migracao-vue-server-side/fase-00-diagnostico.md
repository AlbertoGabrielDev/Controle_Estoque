# Fase 00 - Diagnostico e Baseline

## Objetivo
Fechar um baseline tecnico confiavel antes da migracao, com escopo, riscos e backlog objetivo.

## Snapshot da Base
- Data do levantamento: `2026-02-19`
- Views Blade em `src/resources/views`: `92`
- Paginas Vue em `src/resources/js/Pages`: `32`
- `return view(...)` em controllers: `40`
- `Inertia::render(...)` em controllers: `24`

## Checklist da Fase
- [x] Inventariar modulos em Inertia/Vue.
- [x] Inventariar modulos ainda em Blade.
- [x] Confirmar padrao de referencia (`Clients` + `DataTableService` + `DataTable.vue`).
- [x] Mapear rotas e controllers que ainda retornam `view(...)`.
- [x] Identificar riscos tecnicos atuais.
- [x] Consolidar backlog inicial por modulo.

## Testes PHPUnit (Fase 00)
- [x] Definir comando padrao de teste: `cd src && ./vendor/bin/phpunit`.
- [x] Tentar executar PHPUnit no ambiente atual.
- [ ] Registrar execucao bem-sucedida do PHPUnit nesta fase.

### Resultado da Execucao (Fase 00)
- Tentativa com comando local: `cd src && ./vendor/bin/phpunit`.
- Bloqueio de ambiente identificado:
  - `php` nao esta instalado/disponivel no host (`/usr/bin/env: 'php': No such file or directory`).
  - `docker` tambem nao esta disponivel neste WSL (`docker: command not found`).
- Status desta fase para testes: **bloqueado por ambiente** (execucao nao concluida).

## Inventario de Modulos

### Ja em Vue/Inertia
- `Clients`
- `Segments`
- `Taxes`
- `Dashboard`
- `Calendar`
- `Wpp`
- `Auth`
- `Profile`

### Ainda em Blade (alvo da migracao)
- `categorias`
- `produtos`
- `estoque`
- `fornecedor`
- `marca`
- `usuario`
- `unidades`
- `role`
- `vendas`
- `spreadsheets`

## Backlog de Controllers com Blade (por volume)
- `UsuarioController.php`: `6`
- `CategoriaController.php`: `5`
- `EstoqueController.php`: `5`
- `FornecedorController.php`: `4`
- `MarcaController.php`: `4`
- `ProdutoController.php`: `4`
- `UnidadeController.php`: `4`
- `RoleController.php`: `3`
- `VendaController.php`: `2`
- `SpreadsheetController.php`: `1`
- `MenuController.php`: `1`
- `Api/GraficosApiController.php`: `1`

## Prefixos de Rota Alvo (web.php)
- `/categoria`
- `/produtos`
- `/estoque`
- `/fornecedor`
- `/marca`
- `/usuario`
- `/unidades`
- `/roles`
- `/vendas`
- `/spreadsheet`

## Estado do Padrao Server-Side

### Ja no padrao (ou muito proximo)
- `Clients`: referencia principal.
- `Segments`: DataTable server-side ativo.
- `Taxes`: DataTable server-side ativo.

### Base tecnica pronta para escala
- `DataTable.vue`
- `DataTableService.php`
- `HasDatatableConfig.php`
- `EditButton.vue`
- `ButtonStatus.vue`

## Inconsistencias Encontradas (prioridade alta)
1. `CustomerSegmentController` aponta para paginas inexistentes:
- `Inertia::render('Segmentos/Create')`
- `Inertia::render('Segmentos/Edit')`
- Pasta real atual: `Pages/Segments/*`

2. `ButtonStatus.vue` com fluxo inconsistente:
- Usa `router.post(...)` e tenta ler `res.json()` depois.
- Isso nao e compativel com o retorno do router do Inertia.

3. Legado jQuery ainda acoplado em modulos Blade:
- Uso forte em `src/public/js/app.js`.
- Scripts inline em views complexas (`vendas`, `spreadsheets`).

4. Rota de status sem padrao unico:
- Nomes diferentes por modulo (`cliente.status`, `produto.status`, `unidades.status`, etc).

## Riscos da Migracao
- Risco funcional medio/alto em `vendas` (fluxo de carrinho + scanner + finalizacao).
- Risco medio em `estoque` (calculo e preview de impostos).
- Risco medio em `usuario` (upload de foto + papeis/permissoes).
- Risco baixo em CRUDs simples (`marca`, `unidades`, parte de `categorias`).

## Entregaveis da Fase 00
- Baseline fechado e documentado neste arquivo.
- Matriz de modulos em `modulos-matriz.md`.
- Plano por fases em:
  - `fase-01-foundation-padrao-server-side.md`
  - `fase-02-crud-baixo-risco.md`
  - `fase-03-produtos-estoque.md`
  - `fase-04-vendas-spreadsheets.md`
  - `fase-05-hardening-cutover.md`

## Status da Fase
- **Concluida**.
- Proximo passo: iniciar Fase 01.
