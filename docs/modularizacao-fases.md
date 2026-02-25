# Modularizacao por Modulos (Feature Modules) - Fases

Objetivo: reestruturar o projeto para uma arquitetura por modulo (feature),
mantendo front-end e back-end organizados por dominio e o banco de dados
organizado dentro de cada modulo.

Status atual:
- Fase 0: concluida e revisada (inventario, convencoes e matriz arquitetural por modulo).
- Fase 1: concluida e revisada (infraestrutura base validada com o padrao arquitetural atualizado).
- Fase 2: concluida (modulo `Products` modularizado com wrappers temporarios de compatibilidade).
- Observacao Fase 2: wrappers de `resources/js/Pages/Products/*` permanecem temporariamente por compatibilidade com testes/contratos legados.
- Fase 3: concluida (modulos `Stock`/`Estoque`, `PriceTables` e `Sales` com backend/rotas/pages/DB artifacts co-localizados, wrappers de compatibilidade, smoke tests e testes de fluxo minimos).
- Observacao Fase 3: `Dashboard`/`Calendar` permanecem como componentes compartilhados (`Shared/Core`) nesta etapa, consumidos pelas rotas do modulo `Sales`.
- Fase 4: concluida (modulos `Brands`/`Marcas`, `Categories`/`Categorias`, `Items`/`Itens`, `MeasureUnits`/`UnidadesMedida`, `Units`/`Unidades`, `Suppliers`/`Fornecedores` e `Customers`/`Clientes + Segmentos` finalizados com cutover incremental e wrappers temporarios).
- Fase 5: concluida (modulos `Finance` e `Taxes` com backend/rotas/pages/DB artifacts co-localizados e wrappers temporarios).
- Fase 6: concluida (modulos `Admin` e `Settings` migrados, dominio WhatsApp removido e testes da fase adicionados).
- Fase 7: em andamento (limpeza final: remover artefatos/rotas/dependencias legadas e alinhar documentacao).

Regras gerais:
- Cada fase deve terminar com testes PHPunit (rodar com SQLite via Docker).
- Migrations/seeders/factories devem ficar dentro do modulo correspondente.
- O `migrate` e o `seed` completos so serao executados ao final de todas as fases (ambiente dev/prod).
- Para PHPunit em cada fase, usar apenas migrations/seeders/factories do modulo correspondente.
- Fazer migracao incremental, modulo a modulo, mantendo o sistema funcional.

## Padrao arquitetural por modulo (alvo oficial)
Fluxo padrao:
- Controller (obrigatorio): recebe request HTTP, chama service e retorna Inertia/JSON/redirect.
- FormRequest (obrigatorio quando houver entrada): valida dados de entrada.
- Service (obrigatorio): regra de negocio, orquestracao e transacoes.
- Repository + RepositoryEloquent (condicional): usar quando houver consulta complexa, multiplas fontes de dados ou necessidade de contrato para testes/mocks.
- Model (Eloquent): relacoes, casts, scopes e comportamento de persistencia simples (sem regra de negocio pesada).
- Jobs/Events/Listeners/Commands (opcional): somente quando o modulo realmente usar.

Padronizacao de responsabilidades:
- Controller nao deve conter regra de negocio pesada nem query complexa repetida.
- Service nao deve retornar `response()`/`redirect()`; deve retornar dados/objetos para o controller.
- Repository nao deve receber `Request`/`FormRequest`; deve receber parametros/arrays/DTOs.
- Repository nao deve retornar `response()`/`redirect()` e nao deve conhecer Inertia.
- Transacoes (`DB::transaction`) ficam no Service.
- Validacao fica em FormRequest (ou classe de validacao equivalente do modulo).
- Queries de datatable/reports agregados devem ficar em Repository quando forem complexas ou reutilizadas.

Padronizacao de `Repository` / `RepositoryEloquent` (quando existir):
- `Repository` deve ser contrato real (interface), nao interface vazia se houver metodos customizados.
- `RepositoryEloquent` implementa o contrato e concentra acesso a Eloquent/Query Builder.
- Metodos customizados devem estar declarados no contrato.
- Se o modulo for CRUD simples e nao houver ganho real, nao criar Repository por obrigacao.

Criterio oficial para uso de `Repository`:
- USAR: consultas complexas, joins/agregacoes, filtros reutilizados, mais de uma fonte de dados, necessidade de mocks/fakes.
- NAO USAR (inicialmente): CRUD simples, Eloquent direto sem repeticao, repository pass-through sem regra.

## Matriz de modulos e componentes (padrao recomendado)
Legenda:
- `SIM`: deve existir/usar.
- `OPCIONAL`: criar somente quando houver caso real.
- `NAO`: evitar na primeira versao do modulo.

### Modulos core (negocio principal)
- `Products`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM (consolidar create/update/search/orquestracao)
  - Repository + RepositoryEloquent: SIM (datatable, busca, consultas de produto, filtros)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: datatable, busca API, agregacoes e consultas reutilizaveis.
- `Estoque`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM (entrada/edicao, imposto, fluxo de estoque)
  - Repository + RepositoryEloquent: SIM
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: filtros complexos, historico, joins, datatable, consultas de suporte.
- `Vendas` (inclui carrinho/pedidos):
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM (obrigatorio, modulo transacional)
  - Repository + RepositoryEloquent: SIM, mas por subdominio quando necessario (`Cart`, `Order`, `StockAllocation`/`StockRead`)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: SIM/OPCIONAL (eventos e jobs conforme integracoes)
  - Motivo do Repository: transacoes, consultas de estoque/preco, possivel evolucao de fontes e testes.
- `TabelaPreco`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: SIM
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: consultas por item/produto/marca/fornecedor, regras de combinacao e leitura reutilizada.
- `Taxes` / `TaxRules`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM (calculo/regra fiscal)
  - Repository + RepositoryEloquent: SIM
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: regras/filtros/escopos e consultas de configuracao fiscal.

### Cadastros mestre (CRUDs + datatable)
- `Categorias`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: OPCIONAL (NAO na primeira versao, usar se query de produtos/categoria crescer)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Marcas`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Fornecedores`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: OPCIONAL (usar se consolidar consultas com telefones/cidades/MDM)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Itens`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `UnidadesMedida`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Unidades` (filiais/unidades):
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: OPCIONAL (se centralizar filtros/permissoes por unidade)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Fornecedores`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Clientes`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: SIM
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL

### Clientes, segmentos e relacao comercial
- `Clientes`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: SIM
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: autocomplete, filtros, vinculos de segmento/tabela de preco, consultas de integracao.
- `Segmentos` (`CustomerSegments`):
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Status da Fase 4: cutover modular concluido dentro do modulo `Customers` (wrappers temporarios mantidos).

### Financeiro
- `CentroCusto`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `ContaContabil`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Despesas`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: OPCIONAL (usar se surgirem relatorios/filtros mais complexos)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL

### Admin, acesso e usuarios
- `Usuarios`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: OPCIONAL (datatable + roles pode justificar)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Roles/Permissoes/Menus` (ACL):
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: SIM (principalmente `Roles`/matriz de permissoes)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: joins/pivots de permissao e montagem de matriz ACL.
- `Auth/Profile`:
  - Controller: SIM
  - FormRequest: SIM (quando houver entrada)
  - Service: OPCIONAL (usar quando a regra sair do controller/framework)
  - Repository + RepositoryEloquent: NAO
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL

### Configuracoes e suporte
- `Settings` / `AppSettings` / `SalesSettings`:
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente; Eloquent direto via service)
  - Model: SIM
  - Jobs/Events/Listeners/Commands: OPCIONAL
- `Dashboard` / `Calendar` (relatorios e agregacoes):
  - Controller: SIM
  - FormRequest: OPCIONAL (para filtros de entrada)
  - Service: SIM
  - Repository + RepositoryEloquent: SIM (preferencialmente read-repository para consultas agregadas)
  - Model: OPCIONAL (usa varios models existentes)
  - Jobs/Events/Listeners/Commands: OPCIONAL
  - Motivo do Repository: consultas agregadas e relatorios tendem a crescer e ser reutilizados.
- `Spreadsheets` (importacao/compare):
  - Controller: SIM
  - FormRequest: SIM
  - Service: SIM
  - Repository + RepositoryEloquent: NAO (inicialmente)
  - Model: OPCIONAL
  - Jobs/Events/Listeners/Commands: SIM (jobs de importacao fazem sentido)
- `WhatsApp / Marketing / MessageTemplates` (DESCONTINUADO - REMOVER):
  - Decisao: NAO modularizar este dominio neste projeto.
  - Acao planejada: remover 100% o modulo quando chegarmos na fase correspondente.
  - Escopo de remocao: controllers, services, models, rotas, views/pages, components, migrations, seeders, factories, jobs, events, listeners, commands e configuracoes relacionadas.
  - Dependencias cruzadas: remover tambem metodos, componentes, menus, imports, chamadas e integracoes em outros modulos que apontem para WhatsApp/Marketing/MessageTemplates.
  - Testes: adicionar/ajustar PHPUnit da fase para validar que nao ficaram referencias ativas (rotas/imports/componentes) desse dominio.

### Shared / Core (fora dos modulos de negocio)
- `Shared/Core` (`DataTableService`, `Support`, `Enums`, `Criteria`, `Providers`, middlewares, layouts/components globais):
  - Nao sao modulos de dominio.
  - Nao usar Repository/RepositoryEloquent.
  - Manter como infraestrutura compartilhada.

## Checklist de padronizacao por modulo (execucao)
Checklist minimo para cada modulo:
- Definir se o modulo e `CRUD simples`, `consulta complexa` ou `transacional`.
- Criar `Controller`, `FormRequest` (quando houver entrada) e `Service`.
- Decidir `Repository + RepositoryEloquent` com base no criterio oficial acima.
- Garantir que `Repository` (quando existir) seja contrato real e sem logica HTTP.
- Garantir que `Service` concentre transacoes e orquestracao.
- Garantir que `Model` fique sem regra de negocio pesada.
- Adicionar PHPUnit do modulo/fase.

## Fase 0 - Inventario e convencoes
Entregas:
- Documento de convencoes de pastas por modulo (backend + frontend).
- Lista oficial de modulos e seus limites (scope).
- Definicao do modulo piloto.

PHPUnit:
- Rodar suite atual para baseline com SQLite via Docker.

## Fase 1 - Estrutura base de modulos
Entregas:
- Criar raiz de modulos (ex.: `src/Modules`).
- Definir layout padrao do modulo (Http, Services, Repositories, Models, Front).
- Ajustar autoload/Providers para carregar `Modules` e registrar migrations/seeders/factories por modulo.

PHPUnit:
- Teste smoke para garantir boot da aplicacao.

## Fase 2 - Modulo piloto (ex.: Produtos)
Entregas:
- Mover controller/service/repository/model do modulo piloto.
- Ajustar rotas para apontar para o novo namespace.
- Mover pages do front-end do modulo piloto para estrutura do modulo.

PHPUnit:
- Criar/ajustar testes do modulo piloto e rodar suite.

## Fase 3 - Modulos core de estoque e vendas
Entregas:
- Migrar Estoque, Vendas/Carrinho/Pedidos e Tabelas de Preco.
- Consolidar dependencias entre modulos (ex.: Produtos x Estoque).
- Ajustar requests e services associados.

PHPUnit:
- Testes de fluxo de estoque e venda (minimo 1 teste por modulo).

## Fase 4 - Modulos de cadastros e suporte
Status parcial:
- `Brands`/`Marcas` iniciado (controller/requests/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).
- `Categories`/`Categorias` iniciado (controller/requests/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).
- `Items`/`Itens` iniciado (controller/requests/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).
- `MeasureUnits`/`UnidadesMedida` iniciado (controller/request/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).
- `Units`/`Unidades` iniciado (controller/requests/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).
- `Suppliers`/`Fornecedores` iniciado (controller/requests/service/model/rotas/pages/DB artifacts no modulo + wrappers de compatibilidade).

Entregas:
- Migrar Categorias, Marcas, Fornecedores, Itens, Unidades/UnidadesMedida.
- Migrar Clientes e Segmentos.
- Ajustar seeds/factories dos modulos e padronizar carregamento por modulo.

PHPUnit:
- Testes de CRUD basicos para cada modulo migrado (implementados em `tests/Feature/Modules/PhaseFourCrudTest.php`).

## Fase 5 - Financeiro e Taxas
Entregas:
- Migrar Centros de Custo, Contas Contabeis e Despesas.
- Migrar Taxas/Rules e calculos relacionados.
- Co-localizar rotas, pages e DB artifacts em `Modules/Finance` e `Modules/Taxes`, mantendo wrappers em `App/*`, `database/*` e `resources/js/Pages/*`.

PHPUnit:
- Teste cobrindo 1 regra fiscal (`tests/Feature/Modules/TaxesModulePhaseFiveFlowTest.php`).
- Teste cobrindo 1 fluxo financeiro (`tests/Feature/Modules/FinanceModulePhaseFiveFlowTest.php`).

## Fase 6 - Admin, Auth e Configuracoes
Entregas:
- Migrar Usuarios/Perfis/Permissoes/Menus.
- Migrar Configuracoes e Settings.
- Remover o dominio `WhatsApp / Marketing / MessageTemplates` (descontinuado) e limpar dependencias cruzadas em outros modulos.

PHPUnit:
- Testes de autenticacao basica e permissao (`tests/Feature/Modules/AdminModulePhaseSixAuthTest.php`).
- Teste(s) de regressao para garantir ausencia de referencias/rotas/componentes do dominio removido (`tests/Feature/Modules/AdminModulePhaseSixRegressionTest.php`).

## Fase 7 - Limpeza e padronizacao
Entregas:
- Remover pastas antigas ou duplicadas.
- Atualizar documentacao e caminhos.
- Revisao geral de imports/rotas.
Progresso:
- Removido pacote `@wppconnect-team/wppconnect` de `package.json` (dependencia legada do dominio WhatsApp).
- Removido `resources/js/ziggy.js` gerado com rotas antigas; regenerar via `php artisan ziggy:generate` apos limpar rotas (fase concluida).
Pendencias:
- Atualizar `package-lock.json` com `npm install` (limpeza do pacote removido).
- Regenerar `ziggy.js` apos revisao final das rotas/menus.

PHPUnit:
- Rodar suite completa e corrigir regressao.
