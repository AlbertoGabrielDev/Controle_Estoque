# Implementacao do Modulo de Compras

Data: 2026-02-26
Status geral: Etapas 1-9 concluidas (estrutura base + migrations/models + backend + UI + factories/tests + testes do modulo Purchases).

## Escopo funcional
Fluxo: requisicao -> cotacao -> pedido -> recebimento -> conferencia -> devolucao -> contas a pagar.
Entidades previstas: purchase_requisitions, purchase_requisition_items, purchase_quotations, purchase_quotation_suppliers,
purchase_quotation_supplier_items, purchase_orders, purchase_order_items, purchase_receipts, purchase_receipt_items,
purchase_returns, purchase_return_items, purchase_document_sequences e purchase_payables.

## Decisoes de dominio
- Vencedor por item na cotacao (cada item tem 1 fornecedor selecionado).
- Conferencia de recebimento: estrategia "aceitar divergencia" (mantem pedido; permite devolucao e segue AP).
- Geracao de pedidos: 1 pedido por fornecedor vencedor (mais realista).
- Numeracao: DocumentNumberService usando tabela purchase_document_sequences com lock transacional.
- Integracao Finance/Estoque: Finance nao possui AP, entao purchase_payables foi criado no modulo Purchases. Estoque tera hook/service se necessario.

## Checklist
1. [x] Ler modulos existentes (Brands/Categories/Admin) e mapear padrao.
Arquivos: `src/Modules/Brands/module.json`, `src/Modules/Brands/README.md`, `src/Modules/Brands/Routes/web.php`, `src/Modules/Admin/Routes/web.php`, `src/Modules/Sales/Routes/web.php`, `docs/modularizacao-fases.md`.
Comandos: `Get-ChildItem -Directory -Path src\Modules`, `Get-ChildItem -Force -Path src\Modules\Brands`, `Get-Content ...`.
Problemas: nenhum.
Proximos passos: criar estrutura do modulo Purchases.

2. [x] Criar modulo Purchases (module.json, Routes/web.php, README.md).
Arquivos: `src/Modules/Purchases/module.json`, `src/Modules/Purchases/Routes/web.php`, `src/Modules/Purchases/README.md`.
Comandos: `New-Item -ItemType Directory -Force ...`, `Set-Content ...`.
Problemas: nenhum.
Proximos passos: criar doc de implementacao e listar migrations.

3. [x] Criar `docs/COMPRAS_IMPLEMENTACAO.md` com checklist e planejamento.
Arquivos: `docs/COMPRAS_IMPLEMENTACAO.md`.
Comandos: `Set-Content ...`.
Problemas: nenhum.
Proximos passos: implementar migrations e models.

4. [x] Implementar migrations e models.
Arquivos: `src/Modules/Purchases/Database/Migrations/2026_02_26_000001_create_purchase_document_sequences_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000002_create_purchase_requisitions_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000003_create_purchase_requisition_items_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000004_create_purchase_quotations_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000005_create_purchase_quotation_suppliers_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000006_create_purchase_quotation_supplier_items_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000007_create_purchase_orders_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000008_create_purchase_order_items_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000009_create_purchase_receipts_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000010_create_purchase_receipt_items_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000011_create_purchase_returns_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000012_create_purchase_return_items_table.php`,
`src/Modules/Purchases/Database/Migrations/2026_02_26_000013_create_purchase_payables_table.php`,
`src/Modules/Purchases/Models/PurchaseDocumentSequence.php`,
`src/Modules/Purchases/Models/PurchaseRequisition.php`,
`src/Modules/Purchases/Models/PurchaseRequisitionItem.php`,
`src/Modules/Purchases/Models/PurchaseQuotation.php`,
`src/Modules/Purchases/Models/PurchaseQuotationSupplier.php`,
`src/Modules/Purchases/Models/PurchaseQuotationSupplierItem.php`,
`src/Modules/Purchases/Models/PurchaseOrder.php`,
`src/Modules/Purchases/Models/PurchaseOrderItem.php`,
`src/Modules/Purchases/Models/PurchaseReceipt.php`,
`src/Modules/Purchases/Models/PurchaseReceiptItem.php`,
`src/Modules/Purchases/Models/PurchaseReturn.php`,
`src/Modules/Purchases/Models/PurchaseReturnItem.php`,
`src/Modules/Purchases/Models/PurchasePayable.php`.
Comandos: `Set-Content ...`.
Problemas: nenhum.
Proximos passos: implementar Services/Requests/Controllers e rotas.

5. [x] Implementar services, requests, controllers e rotas.
Arquivos: `src/Modules/Purchases/Services/DocumentNumberService.php`,
`src/Modules/Purchases/Services/PurchaseRequisitionService.php`,
`src/Modules/Purchases/Services/PurchaseQuotationService.php`,
`src/Modules/Purchases/Services/PurchaseOrderService.php`,
`src/Modules/Purchases/Services/PurchaseReceiptService.php`,
`src/Modules/Purchases/Services/PurchaseReturnService.php`,
`src/Modules/Purchases/Services/AccountsPayableIntegrationService.php`,
`src/Modules/Purchases/Http/Requests/PurchaseRequisitionStoreRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseRequisitionUpdateRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseQuotationStoreRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseQuotationUpdateRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseQuotationAddSupplierRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseQuotationPricesRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseQuotationSelectItemRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseOrderFromQuotationRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseReceiptStoreRequest.php`,
`src/Modules/Purchases/Http/Requests/PurchaseReturnStoreRequest.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseRequisitionController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseQuotationController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseOrderController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseReceiptController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseReturnController.php`,
`src/Modules/Purchases/Http/Controllers/PurchasePayableController.php`,
`src/Modules/Purchases/Routes/web.php`.
Comandos: `Set-Content ...`, `apply_patch ...`.
Problemas: nenhum. TODO anotado: integrar devolucao com movimentacao de estoque.
Proximos passos: implementar paginas Vue/Inertia.

6. [x] Implementar paginas Vue/Inertia.
Arquivos: `src/Modules/Purchases/Resources/js/Pages/Purchases/Shared/Pagination.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Requisitions/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Requisitions/Create.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Requisitions/Edit.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Requisitions/Show.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Requisitions/RequisitionForm.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Quotations/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Quotations/Create.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Quotations/Edit.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Quotations/Show.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Quotations/QuotationForm.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Orders/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Orders/Show.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Receipts/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Receipts/Create.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Receipts/Show.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Receipts/ReceiptForm.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Returns/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Returns/Create.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Returns/Show.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Returns/ReturnForm.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Payables/Index.vue`,
`src/Modules/Purchases/Resources/js/Pages/Purchases/Payables/Show.vue`,
`src/Resources/js/app.js`,
`src/Modules/Purchases/Models/PurchaseQuotation.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseRequisitionController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseQuotationController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseOrderController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseReceiptController.php`,
`src/Modules/Purchases/Http/Controllers/PurchaseReturnController.php`,
`src/Modules/Purchases/Http/Controllers/PurchasePayableController.php`.
Comandos: `Set-Content ...`, `New-Item ...`, `apply_patch ...`.
Problemas: nenhum.
Proximos passos: criar factories e testes PHPUnit.

7. [x] Implementar factories e testes PHPUnit.
Arquivos: `src/Modules/Purchases/Database/Factories/PurchaseQuotationSupplierFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseQuotationSupplierItemFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseOrderFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseOrderItemFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseReceiptFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseReceiptItemFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseReturnFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchaseReturnItemFactory.php`,
`src/Modules/Purchases/Database/Factories/PurchasePayableFactory.php`,
`src/tests/Unit/Modules/DocumentNumberServiceTest.php`,
`src/tests/Unit/Modules/PurchaseOrderServiceTest.php`,
`src/tests/Unit/Modules/PurchaseReceiptServiceTest.php`,
`src/tests/Unit/Modules/PurchaseReturnServiceTest.php`,
`src/tests/Feature/Modules/PurchasesModuleFlowTest.php`.
Comandos: `Set-Content ...`, `apply_patch ...`.
Problemas: nenhum.
Proximos passos: rodar `php artisan test` e corrigir falhas.

8. [x] Rodar `php artisan test` e corrigir (somente testes do modulo Purchases).
Arquivos: `src/Modules/Purchases/Services/PurchaseReceiptService.php`,
`src/Modules/Purchases/Services/PurchaseReturnService.php`,
`src/tests/Feature/Modules/PurchasesModuleFlowTest.php`,
`src/tests/Unit/Modules/PurchaseOrderServiceTest.php`,
`src/tests/Unit/Modules/PurchaseReceiptServiceTest.php`,
`src/tests/Unit/Modules/PurchaseReturnServiceTest.php`,
`src/Modules/Purchases/*` (remocao de BOM UTF-8 nos arquivos PHP do modulo).
Comandos: `docker compose exec -T app php artisan test --filter "(DocumentNumberServiceTest|PurchaseOrderServiceTest|PurchaseReceiptServiceTest|PurchaseReturnServiceTest|PurchasesModuleFlowTest)"`.
Problemas: Suite completa permanece com falhas em outros modulos (wrappers legacy ausentes), mas testes do modulo Purchases passaram.
Proximos passos: seguir para etapa 9.

9. [x] Atualizar docs no final com tudo completo.
Arquivos: `docs/COMPRAS_IMPLEMENTACAO.md`,
`src/Modules/Purchases/Database/Seeders/PurchasesSeeder.php`,
`src/database/seeders/PurchasesSeeder.php`,
`src/database/seeders/DatabaseSeeder.php`.
Comandos: `apply_patch ...`.
Problemas: nenhum.
Proximos passos: encerrar ciclo do modulo.

## Migrations planejadas
Ordem sugerida (dependencias):

| Ordem | Migration (arquivo) | Tabelas | Observacao |
| --- | --- | --- | --- |
| 1 | 2026_02_26_000001_create_purchase_document_sequences_table.php | purchase_document_sequences | Sequencia de numeros por tipo. |
| 2 | 2026_02_26_000002_create_purchase_requisitions_table.php | purchase_requisitions | Base da requisicao. |
| 3 | 2026_02_26_000003_create_purchase_requisition_items_table.php | purchase_requisition_items | Itens da requisicao. |
| 4 | 2026_02_26_000004_create_purchase_quotations_table.php | purchase_quotations | Cabecalho da cotacao. |
| 5 | 2026_02_26_000005_create_purchase_quotation_suppliers_table.php | purchase_quotation_suppliers | Fornecedores convidados. |
| 6 | 2026_02_26_000006_create_purchase_quotation_supplier_items_table.php | purchase_quotation_supplier_items | Precos por fornecedor/item. |
| 7 | 2026_02_26_000007_create_purchase_orders_table.php | purchase_orders | Pedido de compra. |
| 8 | 2026_02_26_000008_create_purchase_order_items_table.php | purchase_order_items | Itens do pedido. |
| 9 | 2026_02_26_000009_create_purchase_receipts_table.php | purchase_receipts | Recebimento. |
| 10 | 2026_02_26_000010_create_purchase_receipt_items_table.php | purchase_receipt_items | Itens do recebimento. |
| 11 | 2026_02_26_000011_create_purchase_returns_table.php | purchase_returns | Devolucao. |
| 12 | 2026_02_26_000012_create_purchase_return_items_table.php | purchase_return_items | Itens da devolucao. |
| 13 | 2026_02_26_000013_create_purchase_payables_table.php | purchase_payables | Criado porque Finance nao tem AP. |

## Tabela de status por entidade
| Entidade | Status |
| --- | --- |
| purchase_requisitions | draft, aprovado, cancelado, fechado |
| purchase_quotations | aberta, encerrada, cancelada |
| purchase_quotation_suppliers | convidado, respondeu, recusou |
| purchase_orders | emitido, parcialmente_recebido, recebido, cancelado, fechado |
| purchase_receipts | registrado, conferido, com_divergencia, estornado |
| purchase_returns | aberta, confirmada, cancelada |
| purchase_payables | aberto, pago, cancelado |

## Tabela de rotas (planejada)
Prefixo base: `/verdurao/purchases`.

| Metodo | URI | Nome | Observacao |
| --- | --- | --- | --- |
| GET | /requisitions | purchases.requisitions.index | Listagem com filtros. |
| GET | /requisitions/create | purchases.requisitions.create | Formulario. |
| POST | /requisitions | purchases.requisitions.store | Criar requisicao. |
| GET | /requisitions/{id} | purchases.requisitions.show | Detalhe. |
| GET | /requisitions/{id}/edit | purchases.requisitions.edit | Editar. |
| PUT/PATCH | /requisitions/{id} | purchases.requisitions.update | Atualizar. |
| PATCH | /requisitions/{id}/approve | purchases.requisitions.approve | Aprovar. |
| PATCH | /requisitions/{id}/cancel | purchases.requisitions.cancel | Cancelar. |
| PATCH | /requisitions/{id}/close | purchases.requisitions.close | Fechar. |
| GET | /quotations | purchases.quotations.index | Listagem. |
| GET | /quotations/create | purchases.quotations.create | Criar (de requisicao). |
| POST | /quotations | purchases.quotations.store | Criar cotacao. |
| GET | /quotations/{id} | purchases.quotations.show | Detalhe. |
| GET | /quotations/{id}/edit | purchases.quotations.edit | Editar. |
| PUT/PATCH | /quotations/{id} | purchases.quotations.update | Atualizar. |
| POST | /quotations/{id}/suppliers | purchases.quotations.addSupplier | Adicionar fornecedor. |
| PATCH | /quotations/{id}/suppliers/{quotationSupplierId}/prices | purchases.quotations.registerPrices | Registrar precos. |
| PATCH | /quotations/{id}/close | purchases.quotations.close | Encerrar (validar vencedores). |
| PATCH | /quotations/{id}/cancel | purchases.quotations.cancel | Cancelar. |
| PATCH | /quotations/{id}/select-item | purchases.quotations.selectItem | Selecionar vencedor por item. |
| GET | /orders | purchases.orders.index | Listagem. |
| POST | /orders/from-quotation | purchases.orders.fromQuotation | Gerar pedidos por fornecedor. |
| GET | /orders/{id} | purchases.orders.show | Detalhe. |
| PATCH | /orders/{id}/cancel | purchases.orders.cancel | Cancelar. |
| PATCH | /orders/{id}/close | purchases.orders.close | Fechar. |
| GET | /receipts | purchases.receipts.index | Listagem. |
| GET | /receipts/create | purchases.receipts.create | Registrar recebimento. |
| POST | /receipts | purchases.receipts.store | Criar recebimento. |
| GET | /receipts/{id} | purchases.receipts.show | Detalhe. |
| PATCH | /receipts/{id}/check | purchases.receipts.check | Conferir. |
| PATCH | /receipts/{id}/accept-divergence | purchases.receipts.acceptDivergence | Aceitar divergencia. |
| PATCH | /receipts/{id}/reverse | purchases.receipts.reverse | Estornar. |
| GET | /returns | purchases.returns.index | Listagem. |
| GET | /returns/create | purchases.returns.create | Criar devolucao. |
| POST | /returns | purchases.returns.store | Criar devolucao. |
| GET | /returns/{id} | purchases.returns.show | Detalhe. |
| PATCH | /returns/{id}/confirm | purchases.returns.confirm | Confirmar. |
| PATCH | /returns/{id}/cancel | purchases.returns.cancel | Cancelar. |
| GET | /payables | purchases.payables.index | Se criado. |
| GET | /payables/{id} | purchases.payables.show | Se criado. |

## Como rodar migrations e testes
Comandos (executar no container/app conforme padrao do projeto):

```bash
php artisan migrate
php artisan db:seed --class=PurchasesSeeder
php artisan test
```

Para rodar apenas os testes do modulo Purchases (executado nesta etapa):

```bash
php artisan test --filter "(DocumentNumberServiceTest|PurchaseOrderServiceTest|PurchaseReceiptServiceTest|PurchaseReturnServiceTest|PurchasesModuleFlowTest)"
```

## Proximos passos imediatos
- Opcional: rodar a suite completa quando os wrappers legacy dos outros modulos estiverem disponiveis.
