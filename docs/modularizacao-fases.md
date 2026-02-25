# Modularizacao por Modulos (Feature Modules) - Fases

Objetivo: reestruturar o projeto para uma arquitetura por modulo (feature),
mantendo front-end e back-end organizados por dominio e o banco de dados
organizado dentro de cada modulo.

Status atual:
- Fase 0: concluida (inventario de modulos e alinhamento de regras).
- Fase 1: concluida (infraestrutura base + smoke test de descoberta de modulos).
- Proxima fase: Fase 2 (migracao do modulo piloto `Products`).

Regras gerais:
- Cada fase deve terminar com testes PHPunit (rodar com SQLite via Docker).
- Migrations/seeders/factories devem ficar dentro do modulo correspondente.
- O `migrate` e o `seed` completos so serao executados ao final de todas as fases (ambiente dev/prod).
- Para PHPunit em cada fase, usar apenas migrations/seeders/factories do modulo correspondente.
- Fazer migracao incremental, modulo a modulo, mantendo o sistema funcional.

## Fase 0 - Inventario e convencoes
Entregas:
- Documento de convencoes de pastas por modulo (backend + frontend).
- Lista oficial de modulos e seus limites (scope).
- Definicao do modulo piloto.

PHPUnit:
- Rodar suite atual para baseline com SQLite via Docker.

## Fase 1 - Estrutura base de modulos
Entregas:
- Criar raiz de modulos (ex.: `src/Modules`).
- Definir layout padrao do modulo (Http, Services, Repositories, Models, Front).
- Ajustar autoload/Providers para carregar `Modules` e registrar migrations/seeders/factories por modulo.

PHPUnit:
- Teste smoke para garantir boot da aplicacao.

## Fase 2 - Modulo piloto (ex.: Produtos)
Entregas:
- Mover controller/service/repository/model do modulo piloto.
- Ajustar rotas para apontar para o novo namespace.
- Mover pages do front-end do modulo piloto para estrutura do modulo.

PHPUnit:
- Criar/ajustar testes do modulo piloto e rodar suite.

## Fase 3 - Modulos core de estoque e vendas
Entregas:
- Migrar Estoque, Vendas/Carrinho/Pedidos e Tabelas de Preco.
- Consolidar dependencias entre modulos (ex.: Produtos x Estoque).
- Ajustar requests e services associados.

PHPUnit:
- Testes de fluxo de estoque e venda (minimo 1 teste por modulo).

## Fase 4 - Modulos de cadastros e suporte
Entregas:
- Migrar Categorias, Marcas, Fornecedores, Itens, Unidades/UnidadesMedida.
- Migrar Clientes e Segmentos.
- Ajustar seeds/factories dos modulos e padronizar carregamento por modulo.

PHPUnit:
- Testes de CRUD basicos para cada modulo migrado.

## Fase 5 - Financeiro e Taxas
Entregas:
- Migrar Centros de Custo, Contas Contabeis e Despesas.
- Migrar Taxas/Rules e calculos relacionados.

PHPUnit:
- Testes cobrindo pelo menos 1 regra fiscal e 1 fluxo financeiro.

## Fase 6 - Admin, Auth e Configuracoes
Entregas:
- Migrar Usuarios/Perfis/Permissoes/Menus.
- Migrar Configuracoes e Settings.

PHPUnit:
- Testes de autenticacao basica e permissao.

## Fase 7 - Limpeza e padronizacao
Entregas:
- Remover pastas antigas ou duplicadas.
- Atualizar documentacao e caminhos.
- Revisao geral de imports/rotas.

PHPUnit:
- Rodar suite completa e corrigir regressao.
