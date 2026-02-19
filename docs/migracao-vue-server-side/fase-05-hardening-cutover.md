# Fase 05 - Hardening, Cutover e Limpeza

## Objetivo
Finalizar a migracao com estabilidade, remover legados obsoletos e consolidar o padrao.

## Checklist Tecnico
- [ ] Revisar todos os controllers de modulos de negocio:
  - sem `return view(...)` nas telas migradas.
- [ ] Revisar todas as rotas:
  - remover rotas Blade antigas ou redirecionar para rotas Inertia.
- [ ] Excluir os arquivos `.blade.php` obsoletos dos modulos de negocio ja migrados (apos validacao final).
- [ ] Remover/neutralizar scripts legados nao usados (`public/js/app.js`) para modulos migrados.
- [ ] Revisar imports/caminhos de paginas Vue para evitar divergencias de naming.
- [ ] Revisar permissao e menu para todos os modulos migrados.

## Regressao Manual por Modulo
- [ ] Index carrega e pagina corretamente.
- [ ] Filtro funciona e persiste no refresh.
- [ ] Create salva e redireciona corretamente.
- [ ] Edit atualiza corretamente.
- [ ] Status toggle funciona com feedback visual.
- [ ] Permissoes aplicadas corretamente.

## Pos-Migracao
- [ ] Atualizar README tecnico do projeto com novo fluxo padrao.
- [ ] Criar guia rapido para novos modulos seguirem o padrao server-side.
- [ ] Registrar dividas tecnicas remanescentes.

## Criterio de Saida
- Migracao concluida sem regressao funcional critica.
- Base padronizada em Vue/Inertia + server-side DataTable.
- Blade mantido apenas para layout raiz Inertia, emails e arquivos do ecossistema Jetstream/Fortify que nao fazem parte dos modulos de negocio.

## Testes PHPUnit
- [ ] Executar `cd src && ./vendor/bin/phpunit` como gate final da migracao.
- [ ] Garantir suite verde (ou documentar testes legados fora de escopo).
- [ ] Registrar resultado final em `registro-andamento.md`.
