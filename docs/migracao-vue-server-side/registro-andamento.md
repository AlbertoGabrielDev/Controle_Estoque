# Registro de Andamento da Operacao

## Como usar
- Atualizar este arquivo ao final de cada bloco de trabalho.
- Registrar apenas fatos tecnicos relevantes.
- Sempre incluir modulo, fase e proximo passo.

## Template de Registro

### Data
`YYYY-MM-DD`

### Fase
`00 | 01 | 02 | 03 | 04 | 05`

### Modulo
`nome-do-modulo`

### Concluido
- item 1
- item 2

### Em progresso
- item 1

### Bloqueios
- bloqueio 1

### Decisoes tecnicas
- decisao 1

### Proximo passo imediato
- passo 1

---

## Log

### 2026-02-19
### Fase
02
### Modulo
crud-baixo-risco-testes-phase2
### Concluido
- Criada suite dedicada da fase 2 com grupo `phase2`:
  - `src/tests/Unit/Phase2DatatableContractsTest.php`
  - `src/tests/Feature/Phase2InertiaComponentsTest.php`
- Cobertura adicionada para:
  - contrato `dtColumns/dtFilters` dos modelos da onda 1 (`Categoria`, `Marca`, `Unidades`)
  - contrato de componentes Inertia (`Brands/*`, `Units/*`, `Categories/*`)
  - contrato de rotas server-side DataTable (`marca.data`, `unidade.data`, `categoria.data`)
  - existencia dos componentes Vue criados na fase 2 (onda 1)
- Plano da fase 2 atualizado com bloco explicito de testes e comando isolado `--group phase2`.
### Em progresso
- Execucao efetiva da suite `phase2` pendente neste host.
### Bloqueios
- Ambiente local sem runtime/dependencias PHP para executar PHPUnit.
### Decisoes tecnicas
- Testes da fase 2 seguem o mesmo padrao de governanca da fase 1 (`Group` dedicado por fase).
### Proximo passo imediato
- Rodar `phpunit --group phase2` em ambiente com PHP/vendor disponivel e corrigir eventuais falhas.

### 2026-02-19
### Fase
02
### Modulo
crud-baixo-risco-wave1-marca-unidades-categorias
### Concluido
- Migrados os controllers `MarcaController`, `UnidadeController` e `CategoriaController` para `Inertia::render(...)` nas telas principais (`index`, `cadastro`, `editar`).
- Implementados endpoints server-side DataTable:
  - `marca.data`
  - `unidade.data`
  - `categoria.data`
- Padronizado backend das listagens para `DataTableService` + `HasDatatableConfig` + `DataTableActions`.
- Criadas paginas Vue da onda 1 da fase 2:
  - `src/resources/js/Pages/Brands/*`
  - `src/resources/js/Pages/Units/*`
  - `src/resources/js/Pages/Categories/*`
- `categoria.inicio` migrado para Vue (`Categories/Home`) mantendo grid/listagem de categorias com contador de produtos.
- Rotas web atualizadas para conectar as novas telas e rotas `*.data`, mantendo nomenclatura legada de `route name`.
- Build frontend validado com sucesso:
  - comando executado: `cd src && cmd /c npm run -s build`
### Em progresso
- Fase 2 segue aberta para migracao de:
  - `fornecedor`
  - `role`
  - `usuario`
### Bloqueios
- PHPUnit nao executado neste host por indisponibilidade de runtime/dependencias PHP no ambiente atual.
- Tentativas locais no Windows atual:
  - `./vendor/bin/phpunit` (comando nao encontrado no PowerShell)
  - `cmd /c vendor\\bin\\phpunit.bat` (caminho inexistente; `vendor/` indisponivel no ambiente)
### Decisoes tecnicas
- Iniciar a fase 2 por onda de menor risco (`marca`, `unidades`, `categorias`) para estabilizar o fluxo antes de `fornecedor/role/usuario`.
- Manter nomes de rotas legados e introduzir apenas endpoints `*.data` para minimizar regressao em menus/permissoes.
### Proximo passo imediato
- Iniciar onda 2 da fase 2 com migracao de `fornecedor`, seguida de `role` e `usuario`.

### 2026-02-19
### Fase
00
### Modulo
baseline-geral
### Concluido
- Inventario tecnico fechado com snapshot da base (`92` blades, `32` paginas Vue, `40` `return view`, `24` `Inertia::render`).
- Backlog inicial de controllers Blade consolidado por volume.
- Prefixos de rotas alvo da migracao consolidados.
- Matriz de modulos e plano por fases revisados.
- Bloco de testes PHPUnit adicionado em todas as fases do plano.
- Comando executado na fase 00: `cd src && ./vendor/bin/phpunit` (falhou por ambiente: `/usr/bin/env: 'php': No such file or directory`).
### Em progresso
- Nenhum item pendente da fase 00.
### Bloqueios
- Sem runtime PHP no host para executar PHPUnit localmente.
- Docker indisponivel neste WSL para executar PHPUnit em container.
### Decisoes tecnicas
- Migracao sera executada em ondas por risco.
- Padrao de referencia oficial: `Clients` + `DataTableService` + `DataTable.vue`.
### Proximo passo imediato
- Executar fase 01 com ajustes estruturais compartilhados.

### 2026-02-19
### Fase
01
### Modulo
foundation-padrao-server-side
### Concluido
- Corrigido fluxo do `ButtonStatus.vue` para requisicao JSON real (sem `res.json()` via Inertia router).
- Adicionado `meta csrf-token` em `src/resources/views/app.blade.php` para padronizar postbacks via fetch/axios.
- Criado helper unico `src/app/Support/DataTableActions.php` e aplicado em:
  - `ClienteController`
  - `CustomerSegmentController`
  - `TaxRuleController`
  - `ProdutoRepository`
- Corrigidos caminhos Inertia inconsistentes:
  - `Segmentos/*` -> `Segments/*`
  - `Contacts/Contacts` -> `Wpp/Contacts/Contacts`
- Padronizado sync de filtros via querystring com composable:
  - `src/resources/js/composables/useQueryFilters.js`
  - aplicado em `Clients/Index.vue` e `Segments/Index.vue`
- Atualizado `DataTable.vue` para respeitar `searching` configurado e recarregar `ajaxParams` com watch `deep`.
- Adicionado `HasDatatableConfig` + `dtColumns/dtFilters` nos modelos base da proxima onda:
  - `Categoria`, `Fornecedor`, `Marca`, `Unidades`, `Role`, `User`
- Criada suite PHPUnit dedicada da fase 1 (grupo `phase1`) com cobertura de:
  - contrato do helper `DataTableActions`
  - contrato `dtColumns/dtFilters` dos modelos foundation
  - naming de componentes Inertia corrigidos em `Segments/*` e `Wpp/Contacts/Contacts`
  - arquivos: `src/tests/Unit/ExampleTest.php` e `src/tests/Feature/ExampleTest.php`
- Build frontend validado com sucesso:
  - `cd src && npm run -s build`
### Em progresso
- Preparacao da fase 02 (migracao CRUD baixo risco).
### Bloqueios
- PHPUnit segue bloqueado por ambiente:
  - `/usr/bin/env: 'php': No such file or directory`
- Execucao local do grupo `phase1` via Docker nao validada neste ambiente do agente:
  - `docker` indisponivel no WSL atual (sem integracao ativa).
### Decisoes tecnicas
- Acoes de DataTable passam a usar builder server-side unico para reduzir divergencia entre modulos.
- Nome de rota de status deve ser sempre explicito na construcao de acoes (sem inferencia fraca por nome de model).
### Proximo passo imediato
- Iniciar migracao dos CRUDs de baixo risco com o padrao foundation consolidado.
