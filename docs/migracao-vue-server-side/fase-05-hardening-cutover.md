# Fase 05 - Hardening, Cutover e Limpeza

## Objetivo
Finalizar a migracao com estabilidade, remover legados obsoletos e consolidar o padrao.

## Checklist Tecnico
- [x] Revisar todos os controllers de modulos de negocio migrados:
  - sem `return view(...)` nas telas migradas (incluindo `fornecedor`, `usuario`, `role`, `categoria.produto` e `estoque.historico`).
- [x] Revisar todas as rotas migradas:
  - rotas legadas de historico de vendas redirecionadas para rota Inertia principal.
- [x] Excluir os arquivos `.blade.php` obsoletos dos modulos de negocio migrados.
- [ ] Remover/neutralizar scripts legados nao usados (`public/js/app.js`) para modulos migrados.
- [x] Revisar imports/caminhos de paginas Vue para evitar divergencias de naming.
- [x] Revisar permissao e menu para modulos migrados no Inertia share.

## Regressao Manual por Modulo
- [ ] Index carrega e pagina corretamente.
- [ ] Filtro funciona e persiste no refresh.
- [ ] Create salva e redireciona corretamente.
- [ ] Edit atualiza corretamente.
- [ ] Status toggle funciona com feedback visual.
- [ ] Permissoes aplicadas corretamente.

## Pos-Migracao
- [x] Atualizar README tecnico do projeto com novo fluxo padrao.
- [x] Criar guia rapido para novos modulos seguirem o padrao server-side.
- [x] Registrar dividas tecnicas remanescentes.

## Criterio de Saida
- Migracao concluida sem regressao funcional critica.
- Base padronizada em Vue/Inertia + server-side DataTable.
- Blade mantido apenas para layout raiz Inertia, emails e arquivos do ecossistema Jetstream/Fortify que nao fazem parte dos modulos de negocio.

## Testes PHPUnit
- [x] Criar suite dedicada da fase 5 com grupo `phase5` em:
  - `src/tests/Unit/Phase5CutoverContractsTest.php`
  - `src/tests/Feature/Phase5InertiaHardeningTest.php`
- [x] Definir comando de execucao isolada da fase 5:
  - `docker compose exec -T app sh -lc 'cd /var/www/html && php vendor/bin/phpunit --group phase5'`
- [ ] Executar `cd src && ./vendor/bin/phpunit` como gate final da migracao.
- [ ] Garantir suite verde (ou documentar testes legados fora de escopo).
- [ ] Registrar resultado final em `registro-andamento.md`.
