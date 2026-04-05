# Commercial Module

Modulo CRM leve/comercial: oportunidades -> proposta -> pedido de venda -> faturamento -> devolucao -> contas a receber.

Status desta etapa: Fluxo principal implementado e coberto por testes do modulo (2026-04-05).

## O que foi criado
- Estrutura base do modulo (module.json, README.md, Routes/web.php)
- docs/COMERCIAL_IMPLEMENTACAO.md com checklist e plano completo
- Migrations, Models, Repositories, Services, Controllers, Requests e Routes
- Vue/Inertia pages para todos os recursos, incluindo componentes Shared
- Factories e testes PHPUnit do modulo Commercial
- Seeders do modulo:
  - `CommercialDocumentSequenceSeeder` (sequencias OPP/PROP/SO/INV/RET/AR)
  - `CommercialDiscountPolicySeeder` (politicas padrao de desconto)
  - `CommercialFlowDemoSeeder` (fluxo demo fim-a-fim)
  - `CommercialSeeder` (orquestrador)
- Wrappers em `database/seeders` para execucao via `php artisan db:seed --class=...`

## Observacoes de compatibilidade
- Finance nao possui contas a receber: `sales_receivables` reside neste modulo
- Sales modulo tem `orders`/`order_items` (PDV legado) — sem conflito, tabelas distintas
- Models prefixados `Commercial*` para evitar colisao com `App\Models\Order`
- PricingPolicyService integra com modulo PriceTables (tabela_preco_itens)
- FK de cliente usa `clientes.id_cliente` como referencia

## Proximas iteracoes
- Integracao de saida de estoque ao emitir fatura (TODO no InvoiceService)
- Dashboard/kanban de oportunidades por estagio
- Relatorios de pipeline e conversao
- Integracao fiscal (NF-e) futura
- Saneamento da suite legada global fora do escopo do modulo Commercial

## Comandos de seeding
- `docker compose exec -T app php artisan db:seed --class=CommercialSeeder`
- `docker compose exec -T app php artisan db:seed --class=CommercialDocumentSequenceSeeder`
- `docker compose exec -T app php artisan db:seed --class=CommercialDiscountPolicySeeder`
- `docker compose exec -T app php artisan db:seed --class=CommercialFlowDemoSeeder`
