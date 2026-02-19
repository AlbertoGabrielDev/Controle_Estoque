# Matriz de Modulos e Status de Migracao

| Modulo | Estado Atual | Alvo | Fase |
|---|---|---|---|
| Clients | Vue/Inertia + server-side | Consolidado | 01 |
| Segments | Vue/Inertia + server-side | Consolidado | 01 |
| Taxes | Vue/Inertia + server-side | Consolidado | 01 |
| Marca | Vue/Inertia + server-side | Consolidado | 02 |
| Unidades | Vue/Inertia + server-side | Consolidado | 02 |
| Categorias | Vue/Inertia + server-side | Consolidado | 02 |
| Fornecedor | Blade | Vue/Inertia + server-side | 02 (pendente) |
| Role | Blade | Vue/Inertia + server-side | 02 (pendente) |
| Usuario | Blade | Vue/Inertia + server-side | 02 (pendente) |
| Produtos | Vue/Inertia + server-side padrao | Consolidado | 03 |
| Estoque | Vue/Inertia + server-side padrao | Consolidado | 03 |
| Vendas | Vue/Inertia componentizado | Consolidado | 04 |
| Spreadsheets | Vue/Inertia componentizado | Consolidado | 04 |

## Observacoes
- Auth/Profile/Wpp ja estao em Inertia e nao entram como foco principal da migracao de modulos de negocio.
- Blade de componentes Jetstream/Fortify e emails permanece fora do escopo de remocao nesta operacao.
- Na fase 05 foi iniciado o cutover com limpeza de blades obsoletos dos modulos ja migrados.
