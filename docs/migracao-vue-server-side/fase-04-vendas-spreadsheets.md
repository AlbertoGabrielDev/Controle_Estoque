# Fase 04 - Vendas e Spreadsheets (Fluxos Complexos)

## Objetivo
Migrar telas com alto acoplamento em JavaScript legacy para Vue componentizado, mantendo comportamento atual.

## Vendas

### Frontend
- [ ] Criar `Pages/Sales/Index.vue` para registro de venda.
- [ ] Quebrar fluxo em componentes:
  - [ ] `ClientSelector.vue`
  - [ ] `ManualCodeInput.vue`
  - [ ] `QrScanner.vue`
  - [ ] `CartTable.vue`
  - [ ] `RecentSalesTable.vue`
- [ ] Migrar script inline (atual em Blade) para composable `useCart`.

### Backend
- [ ] Converter `vendas()` para `Inertia::render`.
- [ ] Manter endpoints de carrinho/produto/finalizacao existentes.
- [ ] Revisar padrao de resposta JSON para consistencia de erros/sucesso.

### Requisitos funcionais
- [ ] Leitura por codigo manual.
- [ ] Leitura por QR code.
- [ ] Carrinho com incremento/decremento/remocao.
- [ ] Finalizacao de venda com validacao de estoque.

## Spreadsheets

### Frontend
- [ ] Criar `Pages/Spreadsheets/Index.vue`.
- [ ] Migrar upload duplo, preview de colunas e comparador para componentes Vue.
- [ ] Remover dependencia de script inline jQuery nesta tela.

### Backend
- [ ] Converter `SpreadsheetController@index` para Inertia.
- [ ] Manter endpoints de upload/read/compare.
- [ ] Validar limites de carga e experiencia em arquivos grandes.

## Criterio de Saida
- Vendas e Spreadsheets operando em Vue/Inertia.
- Nenhuma dependencia de script inline Blade nesses modulos.
- Fluxos complexos preservados com mesma regra de negocio.

## Testes PHPUnit
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas aos fluxos de vendas/spreadsheets.
- [ ] Registrar resultado da execucao em `registro-andamento.md`.
