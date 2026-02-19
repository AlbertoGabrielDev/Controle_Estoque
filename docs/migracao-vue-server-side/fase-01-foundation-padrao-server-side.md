# Fase 01 - Foundation do Padrao Server-Side

## Objetivo
Fechar as convencoes tecnicas unicas para que todas as migracoes de modulo sigam o mesmo padrao.

## Escopo
- Componente de tabela server-side.
- Contrato de payload de listagem.
- Acoes padrao de linha (editar/status).
- Estrutura de rotas e controllers.

## Frontend - Tarefas
- [x] Padronizar import e uso de `DataTable.vue` nos modulos server-side ja existentes (`Clients`, `Segments`, `Taxes`).
- [x] Definir estrutura unica de colunas por alias:
  - `id`, `c1`, `c2`, ..., `st`, `acoes`.
- [x] Corrigir `ButtonStatus.vue` para fluxo de toggle sem bug de `res.json()`.
- [x] Definir comportamento unico para filtros:
  - querystring
  - preserve state
  - reload de DataTable com `ajaxParams`.
- [x] Definir base visual de `Index` (header, filtros, tabela, botoes).

## Backend - Tarefas
- [x] Definir padrao de endpoints por modulo base (`index`, `data`, `create`, `store`, `edit`, `update`, `destroy` e `show` quando aplicavel).
  - `index`
  - `data`
  - `create`
  - `store`
  - `edit`
  - `update`
  - `destroy`
  - `show` (quando necessario)
- [x] Padronizar uso de `DataTableService::make(...)` nos modulos server-side ativos.
- [x] Adicionar `HasDatatableConfig` nos modelos base dos proximos modulos (`Categoria`, `Fornecedor`, `Marca`, `Unidades`, `Role`, `User`).
- [x] Criar `dtColumns()` e `dtFilters()` para modelos alvo da proxima onda.
- [x] Padronizar render da coluna `acoes` com helper unico (`DataTableActions`).

## Governanca de Codigo
- [x] Garantir naming consistente de paginas Vue e `Inertia::render` (correcao em `Segments/*` e `Wpp/Contacts/*`).
- [x] Revisar `route names` de status para evitar variacoes desnecessarias no frontend/acoes (uso explicito de route name nos builders de acao).
- [x] Evitar dependencia de scripts legados em `public/js/app.js` para telas migradas.

## Criterio de Saida
- Existe um template tecnico unico aprovado para qualquer modulo novo:
  - Index server-side pronto
  - backend `data` padronizado
  - botoes de acao padronizados
  - sem codigo ad hoc por modulo.

## Testes PHPUnit
- [x] Executar `cd src && ./vendor/bin/phpunit` ao final da fase (tentativa executada).
- [ ] Corrigir falhas relacionadas ao escopo da fase.
- [x] Registrar resultado da execucao em `registro-andamento.md`.
- [x] Criar suite dedicada da fase 1 com grupo `phase1` em:
  - `src/tests/Unit/ExampleTest.php`
  - `src/tests/Feature/ExampleTest.php`
- [x] Definir comando de execucao isolada da fase 1 no Docker:
  - `docker compose exec -T app sh -lc 'cd /var/www/html && php vendor/bin/phpunit --group phase1'`

### Resultado de Validacao da Fase 01
- Build frontend executado com sucesso: `cd src && npm run -s build`.
- PHPUnit bloqueado por ambiente: `/usr/bin/env: 'php': No such file or directory`.
