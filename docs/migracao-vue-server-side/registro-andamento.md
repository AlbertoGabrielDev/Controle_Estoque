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
