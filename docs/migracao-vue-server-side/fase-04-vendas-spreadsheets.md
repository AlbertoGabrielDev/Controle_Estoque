# Fase 04 - Vendas e Spreadsheets (Fluxos Complexos)

## Objetivo
Migrar telas com alto acoplamento em JavaScript legacy para Vue componentizado, mantendo comportamento atual.

## Vendas

### Frontend
- [x] Criar `Pages/Sales/Index.vue` para registro de venda.
- [x] Quebrar fluxo em componentes:
  - [x] `ClientSelector.vue`
  - [x] `ManualCodeInput.vue`
  - [x] `QrScanner.vue`
  - [x] `CartTable.vue`
  - [x] `RecentSalesTable.vue`
- [x] Migrar script inline (atual em Blade) para composable `useCart`.

### Backend
- [x] Converter `vendas()` para `Inertia::render`.
- [x] Manter endpoints de carrinho/produto/finalizacao existentes.
- [x] Revisar padrao de resposta JSON para consistencia de erros/sucesso.

### Requisitos funcionais
- [x] Leitura por codigo manual.
- [x] Leitura por QR code.
- [x] Carrinho com incremento/decremento/remocao.
- [x] Finalizacao de venda com validacao de estoque.

## Spreadsheets

### Frontend
- [x] Criar `Pages/Spreadsheets/Index.vue`.
- [x] Migrar upload duplo, preview de colunas e comparador para componentes Vue.
- [x] Remover dependencia de script inline jQuery nesta tela.

### Backend
- [x] Converter `SpreadsheetController@index` para Inertia.
- [x] Manter endpoints de upload/read/compare.
- [x] Validar limites de carga e experiencia em arquivos grandes.

## Criterio de Saida
- Vendas e Spreadsheets operando em Vue/Inertia.
- Nenhuma dependencia de script inline Blade nesses modulos.
- Fluxos complexos preservados com mesma regra de negocio.

## Testes PHPUnit
- [x] Criar suite dedicada da fase 4 com grupo `phase4` em:
  - `src/tests/Unit/Phase4ComponentContractsTest.php`
  - `src/tests/Feature/Phase4InertiaComponentsTest.php`
- [x] Definir comando de execucao isolada da fase 4:
  - `docker compose exec -T app sh -lc 'cd /var/www/html && php vendor/bin/phpunit --group phase4'`
- [ ] Executar `cd src && ./vendor/bin/phpunit` ao final da fase.
- [ ] Corrigir falhas relacionadas aos fluxos de vendas/spreadsheets.
- [x] Registrar progresso tecnico em `registro-andamento.md`.
