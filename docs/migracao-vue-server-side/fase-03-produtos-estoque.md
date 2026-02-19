# Fase 03 - Produtos e Estoque (Regra de Negocio Media/Alta)

## Objetivo
Migrar `produtos` e `estoque` para Vue/Inertia mantendo regras de negocio, filtros e visual atual.

## Produtos

### Frontend
- [x] Criar `Pages/Products/Index.vue` com DataTable server-side.
- [x] Criar `Pages/Products/Create.vue`.
- [x] Criar `Pages/Products/Edit.vue`.
- [x] Criar `Pages/Products/ProductForm.vue`.

### Backend
- [x] Converter controller para `Inertia::render`.
- [x] Padronizar endpoint `produtos.data` para `DataTableService`.
- [x] Migrar/normalizar regras de filtro e ordenacao hoje no repositorio.
- [x] Confirmar validacoes de cadastro/edicao (`ValidacaoProduto*`).

### Requisitos funcionais
- [x] Manter exibicao de informacao nutricional.
- [x] Manter troca de categoria (pivot `categoria_produtos`).
- [x] Manter status toggle.

## Estoque

### Frontend
- [x] Criar `Pages/Stock/Index.vue` com filtros completos + DataTable server-side.
- [x] Criar `Pages/Stock/Create.vue`.
- [x] Criar `Pages/Stock/Edit.vue`.
- [x] Criar `Pages/Stock/StockForm.vue`.
- [x] Criar componente Vue para preview de impostos (substitui partial Blade).

### Backend
- [x] Converter controller para Inertia em `index/cadastro/editar`.
- [x] Manter endpoint `calc-impostos` e adaptar resposta para consumo Vue.
- [x] Garantir consistencia entre `id_tax_fk`, `imposto_total`, `impostos_json`.
- [x] Avaliar migracao de `buscarComFiltros` para `dtFilters`.

### Requisitos funcionais
- [x] Nao quebrar calculo de impostos com `TaxCalculatorService`.
- [x] Nao quebrar historico e edicao de estoque.
- [x] Manter fluxo de fornecedor/marca/produto.

## Criterio de Saida
- Produtos e Estoque sem telas Blade operacionais.
- Index de ambos 100% server-side pelo padrao unico.
- Preview/gravacao de impostos funcionando no fluxo Vue.

## Testes PHPUnit
- [x] Criar suite dedicada da fase 3 com grupo `phase3` em:
  - `src/tests/Unit/Phase3DatatableContractsTest.php`
  - `src/tests/Feature/Phase3InertiaComponentsTest.php`
- [x] Definir comando de execucao isolada da fase 3:
  - `docker compose exec -T app sh -lc 'cd /var/www/html && php vendor/bin/phpunit --group phase3'`
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas a produtos/estoque e calculo de impostos.
- [ ] Registrar resultado da execucao em `registro-andamento.md`.
