# Matriz de Modulos e Status de Migracao

| Modulo | Estado Atual | Alvo | Fase |
|---|---|---|---|
| Clients | Vue/Inertia + server-side | Consolidar padrao | 01 |
| Segments | Vue/Inertia + server-side parcial | Ajustar padrao e naming | 01 |
| Taxes | Vue/Inertia + server-side | Consolidar padrao | 01 |
| Marca | Blade | Vue/Inertia + server-side | 02 |
| Unidades | Blade | Vue/Inertia + server-side | 02 |
| Categorias | Blade | Vue/Inertia + server-side | 02 |
| Fornecedor | Blade | Vue/Inertia + server-side | 02 |
| Role | Blade | Vue/Inertia + server-side | 02 |
| Usuario | Blade | Vue/Inertia + server-side | 02 |
| Produtos | Blade + data server-side legado | Vue/Inertia + server-side padrao | 03 |
| Estoque | Blade + filtros/pag local | Vue/Inertia + server-side padrao | 03 |
| Vendas | Blade + JS inline complexo | Vue/Inertia componentizado | 04 |
| Spreadsheets | Blade + JS inline | Vue/Inertia componentizado | 04 |

## Observacoes
- Auth/Profile/Wpp ja estao em Inertia e nao entram como foco principal da migracao de modulos de negocio.
- Blade de componentes Jetstream/Fortify e emails permanece fora do escopo de remocao nesta operacao.
