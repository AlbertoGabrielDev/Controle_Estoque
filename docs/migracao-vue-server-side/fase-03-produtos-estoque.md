# Fase 03 - Produtos e Estoque (Regra de Negocio Media/Alta)

## Objetivo
Migrar `produtos` e `estoque` para Vue/Inertia mantendo regras de negocio, filtros e visual atual.

## Produtos

### Frontend
- [ ] Criar `Pages/Products/Index.vue` com DataTable server-side.
- [ ] Criar `Pages/Products/Create.vue`.
- [ ] Criar `Pages/Products/Edit.vue`.
- [ ] Criar `Pages/Products/ProductForm.vue`.

### Backend
- [ ] Converter controller para `Inertia::render`.
- [ ] Padronizar endpoint `produtos.data` para `DataTableService`.
- [ ] Migrar/normalizar regras de filtro e ordenacao hoje no repositorio.
- [ ] Confirmar validacoes de cadastro/edicao (`ValidacaoProduto*`).

### Requisitos funcionais
- [ ] Manter exibicao de informacao nutricional.
- [ ] Manter troca de categoria (pivot `categoria_produtos`).
- [ ] Manter status toggle.

## Estoque

### Frontend
- [ ] Criar `Pages/Stock/Index.vue` com filtros completos + DataTable server-side.
- [ ] Criar `Pages/Stock/Create.vue`.
- [ ] Criar `Pages/Stock/Edit.vue`.
- [ ] Criar `Pages/Stock/StockForm.vue`.
- [ ] Criar componente Vue para preview de impostos (substitui partial Blade).

### Backend
- [ ] Converter controller para Inertia em `index/cadastro/editar`.
- [ ] Manter endpoint `calc-impostos` e adaptar resposta para consumo Vue.
- [ ] Garantir consistencia entre `id_tax_fk`, `imposto_total`, `impostos_json`.
- [ ] Avaliar migracao de `buscarComFiltros` para `dtFilters`.

### Requisitos funcionais
- [ ] Nao quebrar calculo de impostos com `TaxCalculatorService`.
- [ ] Nao quebrar historico e edicao de estoque.
- [ ] Manter fluxo de fornecedor/marca/produto.

## Criterio de Saida
- Produtos e Estoque sem telas Blade operacionais.
- Index de ambos 100% server-side pelo padrao unico.
- Preview/gravacao de impostos funcionando no fluxo Vue.

## Testes PHPUnit
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas a produtos/estoque e calculo de impostos.
- [ ] Registrar resultado da execucao em `registro-andamento.md`.
