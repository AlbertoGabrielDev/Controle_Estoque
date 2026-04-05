# Implementacao do Modulo Commercial

Data: 2026-04-05
Status geral: Fases 0-9 concluidas; fase 10 parcial (suite Commercial ok, suite completa com falhas legadas fora do escopo do modulo); fase 11 (multi-idioma + modo escuro) concluida.

## Objetivo do modulo
CRM leve/comercial cobrindo o fluxo completo:
oportunidades -> proposta comercial -> politica de precos/descontos -> pedido de venda -> faturamento -> devolucao -> contas a receber

## Escopo funcional
Entidades previstas:
- commercial_document_sequences
- commercial_discount_policies
- commercial_opportunities
- commercial_proposals + commercial_proposal_items
- sales_orders + sales_order_items
- sales_invoices + sales_invoice_items
- sales_receivables
- sales_returns + sales_return_items

## Decisoes tecnicas e de dominio
- Finance nao possui estrutura de contas a receber (apenas despesas/custos). Portanto `sales_receivables` foi criado no modulo Commercial. Decisao documentada.
- Sales modulo ja tem `orders` e `order_items` (PDV/carrinho legado). Commercial usa `sales_orders` e `sales_order_items` — nomes distintos, sem conflito de tabela.
- Models prefixados com `Commercial*` para evitar colisao com `App\Models\Order`.
- `clientes` tem primary key `id_cliente` — usado como `cliente_id` nas FKs do Commercial.
- PricingPolicyService: prioridade tabela_preco = proposta > oportunidade > cliente.tabela_preco_id.
- Bindings de repositorio adicionados manualmente no AppServiceProvider.
- Rotas sob `/verdurao/commercial/` com mesmo middleware dos modulos existentes.
- AGENTS.md lido — sem conflito com instrucoes deste documento.

## Status geral
- [x] Fase 0: Leitura de padroes (ja realizada nesta sessao)
- [x] Fase 1: docs/COMERCIAL_IMPLEMENTACAO.md criado (este arquivo)
- [x] Fase 2: Estrutura base do modulo
- [x] Fase 3: Migrations
- [x] Fase 4: Models
- [x] Fase 5: Repositories + AppServiceProvider
- [x] Fase 6: Services
- [x] Fase 7: Requests + Controllers + Routes
- [x] Fase 8: Vue/Inertia pages
- [x] Fase 9: Factories + Testes
- [ ] Fase 10: php artisan test + documentacao final (parcial: Commercial ok; suite completa falha em testes legados)
- [x] Fase 11: Multi-idioma (pt/en/es) + modo escuro no modulo Commercial

## Checklist por fases

### Fase 1 — Estrutura base
- [x] src/Modules/Commercial/module.json
- [x] src/Modules/Commercial/README.md
- [x] src/Modules/Commercial/Routes/web.php

### Fase 2 — Migrations (12 arquivos)
- [x] 000001_create_commercial_document_sequences_table
- [x] 000002_create_commercial_discount_policies_table
- [x] 000003_create_commercial_opportunities_table
- [x] 000004_create_commercial_proposals_table
- [x] 000005_create_commercial_proposal_items_table
- [x] 000006_create_sales_orders_table
- [x] 000007_create_sales_order_items_table
- [x] 000008_create_sales_invoices_table
- [x] 000009_create_sales_invoice_items_table
- [x] 000010_create_sales_receivables_table
- [x] 000011_create_sales_returns_table
- [x] 000012_create_sales_return_items_table

### Fase 3 — Models (12 arquivos)
- [x] CommercialDocumentSequence
- [x] CommercialDiscountPolicy
- [x] CommercialOpportunity
- [x] CommercialProposal
- [x] CommercialProposalItem
- [x] CommercialSalesOrder
- [x] CommercialSalesOrderItem
- [x] CommercialSalesInvoice
- [x] CommercialSalesInvoiceItem
- [x] CommercialSalesReceivable
- [x] CommercialSalesReturn
- [x] CommercialSalesReturnItem

### Fase 4 — Repositories (16 arquivos = 8 pares Interface+Eloquent)
- [x] CommercialOpportunityRepository + Eloquent
- [x] CommercialProposalRepository + Eloquent
- [x] CommercialDiscountPolicyRepository + Eloquent
- [x] CommercialSalesOrderRepository + Eloquent
- [x] CommercialSalesInvoiceRepository + Eloquent
- [x] CommercialSalesReturnRepository + Eloquent
- [x] CommercialSalesReceivableRepository + Eloquent
- [x] CommercialDocumentSequenceRepository + Eloquent
- [x] AppServiceProvider atualizado com 8 bindings

### Fase 5 — Services (8 arquivos)
- [x] CommercialDocumentNumberService
- [x] OpportunityService
- [x] ProposalService
- [x] PricingPolicyService
- [x] SalesOrderService
- [x] InvoiceService
- [x] SalesReturnService
- [x] AccountsReceivableIntegrationService

### Fase 6 — Requests + Controllers + Routes
- [x] 16 FormRequests
- [x] 7 Controllers finos
- [x] Routes/web.php

### Fase 7 — Vue/Inertia Pages
- [x] Opportunities: Index, Create, Edit, Show, OpportunityForm
- [x] Proposals: Index, Create, Edit, Show, ProposalForm
- [x] Orders: Index, Create, Show
- [x] Invoices: Index, Create, Show
- [x] Returns: Index, Create, Show
- [x] Receivables: Index, Show
- [x] Shared: ItemsTable, TotalsFooter, FlowBreadcrumb, StatusBadge

### Fase 11 — Multi-idioma e tema
- [x] Commercial adicionado ao `USE_PRINCIPAL` em `resources/js/app.js` (layout padrao e ThemeToggle aplicados as paginas do modulo).
- [x] Textos das paginas `Commercial` internacionalizados com `$t(...)`.
- [x] Chaves adicionadas em `resources/js/locales/pt.json`, `en.json` e `es.json`.
- [x] Checagem de cobertura de chaves executada: 0 chaves faltantes em pt/en/es para as paginas do modulo.
- [x] Ajustes visuais de dark mode aplicados em cabecalhos, cards, inputs, tabelas e links do modulo.
- [x] Menu lateral atualizado (MenuSeeder): grupo `Commercial` com submenus para Opportunities, Proposals, Sales Orders, Invoices, Sales Returns, Accounts Receivable e Discount Policies.

### Fase 8 — Factories + Testes
- [x] 12 Factories
- [x] 6 Unit Tests (7 criados)
- [x] 1 Feature Test (CommercialModuleFlowTest)
- [x] Seeders modularizados:
  - `CommercialDocumentSequenceSeeder`
  - `CommercialDiscountPolicySeeder`
  - `CommercialFlowDemoSeeder`
  - `CommercialSeeder` (orquestrador)
- [x] `DatabaseSeeder` atualizado para incluir `CommercialSeeder` no seed padrao.

## O que falta neste momento
- Concluir saneamento da suite completa do projeto (`php artisan test`), que atualmente falha por contratos legados fora do modulo Commercial.

## Modelos e tabelas criadas

| Modelo | Tabela | Status possiveis |
| --- | --- | --- |
| CommercialDocumentSequence | commercial_document_sequences | — |
| CommercialDiscountPolicy | commercial_discount_policies | ativo/inativo |
| CommercialOpportunity | commercial_opportunities | novo, em_contato, proposta_enviada, negociacao, ganho, perdido |
| CommercialProposal | commercial_proposals | rascunho, enviada, aprovada, rejeitada, vencida, convertida |
| CommercialProposalItem | commercial_proposal_items | — |
| CommercialSalesOrder | commercial_sales_orders (tabela: sales_orders) | rascunho, confirmado, faturado_parcial, faturado_total, cancelado, fechado |
| CommercialSalesOrderItem | commercial_sales_order_items (tabela: sales_order_items) | — |
| CommercialSalesInvoice | sales_invoices | emitida, parcial, paga, cancelada, estornada |
| CommercialSalesInvoiceItem | sales_invoice_items | — |
| CommercialSalesReceivable | sales_receivables | aberto, recebido, cancelado, estornado |
| CommercialSalesReturn | sales_returns | aberta, confirmada, cancelada |
| CommercialSalesReturnItem | sales_return_items | — |

## Rotas criadas
Prefixo base: `/verdurao/commercial`

| Metodo | URI | Nome |
| --- | --- | --- |
| GET | /opportunities | commercial.opportunities.index |
| GET | /opportunities/data | commercial.opportunities.data |
| GET | /opportunities/create | commercial.opportunities.create |
| POST | /opportunities | commercial.opportunities.store |
| GET | /opportunities/{id} | commercial.opportunities.show |
| GET | /opportunities/{id}/edit | commercial.opportunities.edit |
| PATCH | /opportunities/{id} | commercial.opportunities.update |
| PATCH | /opportunities/{id}/status | commercial.opportunities.status |
| POST | /opportunities/{id}/convert-to-proposal | commercial.opportunities.convertToProposal |
| GET | /proposals | commercial.proposals.index |
| GET | /proposals/data | commercial.proposals.data |
| GET | /proposals/create | commercial.proposals.create |
| POST | /proposals | commercial.proposals.store |
| GET | /proposals/{id} | commercial.proposals.show |
| GET | /proposals/{id}/edit | commercial.proposals.edit |
| PATCH | /proposals/{id} | commercial.proposals.update |
| PATCH | /proposals/{id}/send | commercial.proposals.send |
| PATCH | /proposals/{id}/approve | commercial.proposals.approve |
| PATCH | /proposals/{id}/reject | commercial.proposals.reject |
| POST | /proposals/{id}/convert-to-order | commercial.proposals.convertToOrder |
| GET | /discount-policies | commercial.discount-policies.index |
| GET | /discount-policies/data | commercial.discount-policies.data |
| GET | /discount-policies/create | commercial.discount-policies.create |
| POST | /discount-policies | commercial.discount-policies.store |
| GET | /discount-policies/{id} | commercial.discount-policies.show |
| GET | /discount-policies/{id}/edit | commercial.discount-policies.edit |
| PATCH | /discount-policies/{id} | commercial.discount-policies.update |
| GET | /orders | commercial.orders.index |
| GET | /orders/data | commercial.orders.data |
| GET | /orders/create | commercial.orders.create |
| POST | /orders | commercial.orders.store |
| GET | /orders/{id} | commercial.orders.show |
| PATCH | /orders/{id}/confirm | commercial.orders.confirm |
| PATCH | /orders/{id}/cancel | commercial.orders.cancel |
| GET | /invoices | commercial.invoices.index |
| GET | /invoices/data | commercial.invoices.data |
| GET | /invoices/create | commercial.invoices.create |
| POST | /invoices | commercial.invoices.store |
| GET | /invoices/{id} | commercial.invoices.show |
| PATCH | /invoices/{id}/issue | commercial.invoices.issue |
| PATCH | /invoices/{id}/cancel | commercial.invoices.cancel |
| GET | /returns | commercial.returns.index |
| GET | /returns/data | commercial.returns.data |
| GET | /returns/create | commercial.returns.create |
| POST | /returns | commercial.returns.store |
| GET | /returns/{id} | commercial.returns.show |
| PATCH | /returns/{id}/confirm | commercial.returns.confirm |
| PATCH | /returns/{id}/cancel | commercial.returns.cancel |
| GET | /receivables | commercial.receivables.index |
| GET | /receivables/data | commercial.receivables.data |
| GET | /receivables/{id} | commercial.receivables.show |

## Integracao com modulos existentes

### Finance
Finance nao possui contas a receber. Decisao: `sales_receivables` criado no Commercial.
Integracao futura pode ser feita extraindo para Finance quando necessario.

### Stock
Ao faturar, saida de estoque deve ser disparada. Implementado como TODO no InvoiceService:
- `// TODO: Integrar saida de estoque via StockService quando faturamento for confirmado`

### Customers
Referencia por `cliente_id -> clientes.id_cliente`.
PricingPolicyService le `tabela_preco_id` do cliente como fallback de preco.

### PriceTables
PricingPolicyService consulta `tabela_preco_itens` para resolver preco base.
Prioridade: proposta.tabela_preco_id > oportunidade.tabela_preco_id > cliente.tabela_preco_id.

## Como rodar migrations
```bash
docker compose exec -T app php artisan migrate
```

## Como rodar seeders
```bash
docker compose exec -T app php artisan db:seed --class=CommercialSeeder
docker compose exec -T app php artisan db:seed --class=CommercialDocumentSequenceSeeder
docker compose exec -T app php artisan db:seed --class=CommercialDiscountPolicySeeder
docker compose exec -T app php artisan db:seed --class=CommercialFlowDemoSeeder
docker compose exec -T app php artisan db:seed
```

## Como rodar testes
```bash
docker compose exec -T app php artisan test --filter "Commercial"
```

## Arquivos impactados por fase

### Fase 1
- docs/COMERCIAL_IMPLEMENTACAO.md (este arquivo)
- src/Modules/Commercial/module.json
- src/Modules/Commercial/README.md
- src/Modules/Commercial/Routes/web.php

### Fase 2
- src/Modules/Commercial/Database/Migrations/ (12 arquivos)

### Fase 3
- src/Modules/Commercial/Models/ (12 arquivos)

### Fase 4
- src/Modules/Commercial/Repositories/ (16 arquivos)
- src/app/Providers/AppServiceProvider.php

### Fase 5
- src/Modules/Commercial/Services/ (8 arquivos)

### Fase 6
- src/Modules/Commercial/Http/Requests/ (16 arquivos)
- src/Modules/Commercial/Http/Controllers/ (7 arquivos)

### Fase 7
- src/Modules/Commercial/Resources/js/Pages/Commercial/ (31 arquivos Vue)

### Fase 8
- src/Modules/Commercial/Database/Factories/ (12 arquivos)
- src/Modules/Commercial/Database/Seeders/ (4 arquivos)
- src/database/seeders/CommercialSeeder.php
- src/database/seeders/CommercialDocumentSequenceSeeder.php
- src/database/seeders/CommercialDiscountPolicySeeder.php
- src/database/seeders/CommercialFlowDemoSeeder.php
- src/database/seeders/DatabaseSeeder.php
- src/tests/Unit/Modules/ (7 arquivos Commercial)
- src/tests/Feature/Modules/CommercialModuleFlowTest.php

## Comandos executados
- `find src/Modules/Commercial -maxdepth 4 -type d | sort`
- `rg --files src/Modules/Commercial | sort`
- `rg --files src/tests | rg 'Commercial|commercial'`
- `rg -n "Commercial|commercial" src/app/Providers/AppServiceProvider.php`
- `rg --files src/Modules/Commercial/Database/Seeders | sort`
- `rg --files src/Modules/Commercial/Http/Requests | wc -l`
- `rg --files src/Modules/Commercial/Http/Controllers | wc -l`
- `rg --files src/Modules/Commercial/Services | wc -l`
- `rg --files src/Modules/Commercial/Database/Migrations | wc -l`
- `rg --files src/Modules/Commercial/Models | wc -l`
- `rg --files src/Modules/Commercial/Repositories | wc -l`
- `rg --files src/Modules/Commercial/Database/Factories | wc -l`
- `rg --files src/tests/Unit/Modules | rg 'Commercial' | wc -l`
- `rg --files src/tests/Feature/Modules | rg 'Commercial' | wc -l`
- `docker compose exec -T app php artisan test --filter "Commercial"`
- `docker compose exec -T app php artisan test`

## Problemas encontrados
- Inconsistencia entre planejamento e implementacao atual:
- Planejado: 16 FormRequests. Ajustado para 16.
- Planejado: 6 unit tests de services. Ajustado para 7.
- Planejado: `CommercialSeeder`. Implementado no modulo + wrapper em `database/seeders`.
- Planejado: componentes Vue Shared. Implementados e integrados.
- `php artisan test` completo ainda falha por contratos/falhas legadas fora do modulo Commercial (ex.: wrappers em `App\Http\Controllers\*` nao existentes no projeto atual e rotas de auth/profile nao habilitadas nesse contexto de teste).

## Proximos passos
- Corrigir a suite legada global em etapas separadas, sem regressao no modulo Commercial.
