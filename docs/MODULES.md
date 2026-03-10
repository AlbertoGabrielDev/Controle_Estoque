# Módulos do Sistema (Feature Modules)

O sistema **Controle de Estoque** é subdividido funcionalmente. Abaixo está a lista dos módulos principais e suas respectivas responsabilidades.

---

## 📦 Stock (Estoque)
Responsável pelo controle físico dos produtos armazenados.
- **Alertas Visuais:** Controla o vencimento dos lotes de produtos, sinalizando:
  - 🔴 Vencido.
  - 🟡 Próximo ao vencimento (menos de 7 dias).
  - 🟣 Abaixo do estoque mínimo definido pelo gestor.
- **Movimentações:** Registra entradas e saídas físicas do galpão ou loja.
- **Histórico Auditável:** O sistema salva via *Cache* o histórico de saídas (exclusivo para o perfil Admin).

---

## 🛒 Purchases (Compras)
Gerencia o fluxo completo de relacionamento com os fornecedores e aquisição de novos insumos ou produtos.
- **Ciclo de Vida:** Requisição → Cotação → Pedido (Purchase Order) → Recebimento → Conferência → Devolução.
- **Integração Financeira:** Quando uma compra é "Recebida/Efetivada", o módulo alimenta automaticamente o **Finance** enviando dados para Contas a Pagar.

---

## 💰 Finance (Financeiro)
Onde o dinheiro entra e sai. Responsável pelo fluxo de caixa e organização contábil básica.
- **Centros de Custo (`CostCenters`):** Permite organizar as despesas hierarquicamente (Pai/Filho) para entender de onde vêm os gastos (Ex: Administrativo > RH > Salários).
- **Contas Contábeis (`AccountingAccounts`):** Categorização oficial das finanças.
- **Despesas (`Expenses`):** Lançamentos de contas a pagar, integrado ou não com compras.

---

## 🏷️ Products (Produtos)
O coração comercial do sistema.
- **Cadastro Geral:** Produtos (SKU, NCM, Código de Barras), Marcas (`Brands`) e Categorias (`Categories`).
- **Unidades de Medida (`MeasureUnits`):** Controle de UN, KG, CX, PCT.
- **Ativação:** Permite inativar um produto/marca temporariamente. Administradores enxergam; vendedores não.

---

## 🤝 Customers & Suppliers (Clientes e Fornecedores)
Cadastros centralizados.
- **Fornecedores (`Suppliers`):** Utilizados intensamente no módulo de Compras.
- **Clientes (`Customers`):** Utilizados no módulo de Vendas.

---

## 💼 Sales (Vendas)
Gestão da saída de produtos gerando receita.
- **PDV / Pedidos:** Interface rápida para venda.
- **Tabelas de Preço (`PriceTables`):** Permite ter preços diferentes por perfil de cliente (Atacado, Varejo, VIP).
- **Integração de Estoque:** Ao confirmar a venda, aciona imediatamente a baixa no módulo **Stock**.

---

## ⚙️ Admin & Settings
Gestão do sistema como um todo.
- Controles de acesso, permissões de usuários (RBAC simples com Jetstream/Sanctum).
- Configurações globais do sistema.

***

*(A arquitetura modular permite que, no futuro, possamos extrair um módulo inteiro — como Financeiro — e convertê-lo em um microserviço apenas isolando as rotas de comunicação).*
