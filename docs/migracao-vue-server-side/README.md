# Migracao Completa Blade -> Vue (Inertia) com Server-Side DataTables

## Objetivo
Migrar todos os modulos de UI de Blade para Vue + Inertia, mantendo o visual atual e padronizando listagens em server-side (frontend + backend), usando como referencia o padrao ja aplicado em `Clients/Index.vue`.

## Escopo
- Frontend:
  - Substituir telas Blade de modulos de negocio por paginas Vue.
  - Padronizar `Index`, `Create`, `Edit`, `Show` e `Form` por modulo.
  - Reutilizar `DataTable.vue`, `EditButton.vue` e `ButtonStatus.vue`.
- Backend:
  - Converter controllers para `Inertia::render(...)`.
  - Criar/ajustar endpoints `data` para DataTables server-side.
  - Padronizar query server-side com `DataTableService` + `HasDatatableConfig`.

## Estado Atual (resumo tecnico)
- Ja em Inertia/Vue:
  - `Clients/*`, `Taxes/*`, `Segments/*`, `Dashboard/*`, `Calendar/*`, `Wpp/*`, `Auth/*`, `Profile/*`.
- Ainda em Blade (modulos de negocio):
  - `categorias/*`, `produtos/*`, `estoque/*`, `fornecedor/*`, `marca/*`, `usuario/*`, `unidades/*`, `role/*`, `vendas/*`, `spreadsheets/*`.
- Referencias tecnicas existentes:
  - Front: `src/resources/js/components/DataTable.vue`
  - Back: `src/app/Services/DataTableService.php`
  - Modelo pronto: `src/resources/js/Pages/Clients/Index.vue`

## Regras de Implementacao
1. Toda `Index` deve usar `DataTable.vue` com `serverSide: true`.
2. Toda `Index` deve ter endpoint dedicado `*.data`.
3. Toda acao de tabela deve usar `EditButton.vue` e `ButtonStatus.vue` (nunca HTML solto).
4. Toda listagem deve mapear colunas por alias (`id`, `c1`, `c2`, `st`, `acoes`) no backend.
5. Filtros de tela devem refletir em querystring e recarregar DataTable sem perder estado.
6. Preservar visual atual (classes/tokens e hierarquia de layout).
7. Toda fase deve incluir execucao de testes com PHPUnit e registro do resultado.

## Ordem das Fases
1. `fase-00-diagnostico.md`
2. `fase-01-foundation-padrao-server-side.md`
3. `fase-02-crud-baixo-risco.md`
4. `fase-03-produtos-estoque.md`
5. `fase-04-vendas-spreadsheets.md`
6. `fase-05-hardening-cutover.md`
7. `registro-andamento.md` (atualizacao continua)

## Criterio de Conclusao Global
- Nenhum modulo de negocio renderizando `view(...)` Blade para telas principais.
- Todas as listagens em server-side com DataTable padrao.
- Todas as acoes de editar/status via componentes Vue.
- Rotas antigas Blade removidas ou redirecionadas.
- Checklist de regressao manual concluido por modulo.
- PHPUnit executado e registrado em todas as fases.
