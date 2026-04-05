# Especificação Técnica da API Controle_Estoque (Para Agentes de IA)

Esta documentação é destinada a agentes de IA (LLMs) para que compreendam como consumir os dados do ERP `Controle_Estoque`. 
A API é de **apenas leitura (GET)** e protegida por chave de API.

## Diretrizes de Uso para a IA

1.  **Autenticação**: Todas as requisições devem incluir o Header `X-Bot-Api-Key: {API_KEY}`.
2.  **Busca Flexível**: O parâmetro `search` realiza buscas parciais (LIKE) em nomes, códigos e descrições. Use termos curtos e precisos.
3.  **Encadeamento de Ferramentas**:
    -   Se não souber o ID de um produto, use `/products?search=...` primeiro.
    -   Para saber se há estoque de um produto específico, use `/stock?product_id={id}`.
    -   Para pedidos de um cliente, use `/customers?phone=...` para obter o ID ou CPF e depois chame `/orders`.
4.  **Limitação de Dados**: Se o resultado for muito grande, o campo `count` indicará o total. O limite padrão é 20 itens, expansível até 50 via parâmetro `limit`.

---

## 🛠 Endpoints Disponíveis

Base URL: `/api/bot` (ex.: `http://localhost:8000/api/bot`)

### 1. Produtos (`Products`)
Utilizado para consultar o catálogo de produtos ativos.

-   **GET `/products`**
    -   **Parâmetros**: `search` (string), `category_id` (int), `brand_id` (int), `code` (string), `limit` (int).
    -   **Retorno**: Lista de produtos com ID, código, nome, descrição, unidade, categoria (string com nomes), categorias (array) e marcas.
-   **GET `/products/{id}`**
    -   **Retorno**: Detalhes completos, incluindo descrição técnica e informações nutricionais.

### 2. Estoque e Disponibilidade (`Stock`)
Utilizado para verificar quantidades físicas e preços de venda atuais.

-   **GET `/stock`**
    -   **Parâmetros**: `search` (string), `product_id` (int), `batch` (lote), `min_quantity` (float), `limit` (int).
    -   **Retorno**: Lista de itens em estoque, quantidade, preço de venda (`sell_price`), marca, lote e validade.
-   **GET `/stock/{id}`**
    -   **Retorno**: Detalhes de um registro de estoque específico.

> [!WARNING]
> O **Preço de Custo** e a **Margem de Lucro** são dados sensíveis e estão bloqueados nesta API (não retornados). Use apenas o `sell_price`.

### 3. Categorias (`Categories`)
Utilizado para entender a organização do catálogo.

-   **GET `/categories`**
    -   **Parâmetros**: `search` (string), `limit` (int).
    -   **Retorno**: Lista de categorias (ID, Código, Nome, Tipo).
-   **GET `/categories/{id}`**
    -   **Retorno**: Detalhes da categoria.

### 4. Clientes (`Customers`)
Utilizado para identificar perfis de clientes por telefone ou documento.

-   **GET `/customers`**
    -   **Parâmetros**: `phone` (string - apenas números), `document` (CPF/CNPJ), `name` (string), `email` (string).
    -   **Retorno**: Dados básicos do cliente (ID, Nome, CPF, Telefone, Email, Endereço).
-   **GET `/customers/{id}/summary`**
    -   **Retorno**: Resumo financeiro consolidado (Total de compras, saldo devedor, última compra).

### 5. Vendas e Pedidos (`Sales`)
Utilizado para consultar o histórico de transações.

-   **GET `/orders`**
    -   **Parâmetros**: `customer_cpf` (string), `customer_id` (int), `limit` (int).
    -   **Retorno**: Lista de vendas/pedidos realizados.
-   **GET `/orders/{id}`**
    -   **Retorno**: Detalhes da venda, incluindo lista completa de itens (produto, quantidade, preço unitário, subtotal).

### 6. Financeiro (`Finance`)
Consulta de débitos e créditos.

-   **GET `/finance`**
    -   **Parâmetros**: `customer_cpf` (string), `customer_id` (int).
    -   **Retorno**: Balanço geral do cliente e lista das últimas movimentações financeiras.

### 7. Tabelas de Preço e Cotações (`PriceTables`)
Utilizado para cotações complexas e verificação de preços sazonais.

-   **GET `/price-tables`**
    -   **Retorno**: Dados da tabela de preço ativa e produtos participantes.
-   **GET `/price-tables/quote`**
    -   **Parâmetros**: `items` (JSON string. Ex: `[{"product_id":1,"quantity":5}]`).
    -   **Retorno**: Cálculo total da cotação com base nos preços da tabela ativa.

---

## 📋 Padrão de Resposta (JSON)

### Sucesso (200 OK)
```json
{
  "success": true,
  "data": {
    "products": [...],
    "count": 1
  }
}
```

### Erro (401/404/422)
```json
{
  "success": false,
  "error": "Ocorreu um erro ao processar sua solicitação."
}
```

---

## 💡 Dicas Estratégicas para o Agente
-   **Busca em Cascata**: Se o cliente pergunta "Tem verdura?", a IA deve primeiro tentar `/categories?search=verdura` ou `/products?search=verdura`.
-   **Moeda**: Preços retornam formatados como string (ex: "R$ 5,00") ou float. Priorize retornar ao usuário final o valor formatado.
-   **Segurança**: A API é 100% segura para leitura. Não existe risco de a IA apagar ou alterar nada via estes endpoints.
