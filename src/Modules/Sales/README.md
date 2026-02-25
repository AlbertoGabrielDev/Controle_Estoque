# Sales Module

Modulo de Vendas/Carrinho/Pedidos iniciado na Fase 3.

Status atual da etapa:
- Controllers web/API, requests, service e models de `Sales` migrados para o modulo.
- Rotas web/api de vendas, carrinho e pedidos co-localizadas no modulo.
- Paginas Vue de `Sales` co-localizadas no modulo.
- DB artifacts (`migrations`, `seeders`) migrados para `Database/*` no modulo.
- Repositorios por subdominio adicionados (`Cart`, `Order`, `Venda`) e usados no `VendaService`.
- Testes smoke + fluxo minimo (carrinho) adicionados.
- Wrappers de compatibilidade permanecem em `App/*` e `resources/js/Pages/Sales/*`.

Proximas iteracoes:
- Evoluir read-repositories/listagens (`historico`, dashboard de vendas) conforme necessidade.
