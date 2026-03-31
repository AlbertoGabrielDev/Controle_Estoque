# Integração bot-zap ↔ Controle_Estoque

## Visão Geral

Este documento descreve a integração entre o projeto **bot-zap** (WhatsApp Bot + IA Gemini) e o projeto **Controle_Estoque** (ERP de gestão).

O objetivo é permitir que o agente de IA do bot-zap consulte dados reais do ERP — como produtos, estoque, preços, pedidos, clientes e informações financeiras — para responder clientes pelo WhatsApp.

### Diagrama de Comunicação

```text
Cliente WhatsApp
    → bot-zap (WPPConnect → ChatbotSessionService → AiAgentManager)
        → Tool call (ex.: search_products)
            → HTTP GET ao Controle_Estoque (API REST)
                → Controle_Estoque responde JSON
            ← Tool retorna dados ao agente
        ← Gemini responde ao cliente
    → WhatsApp reply
```

---

## Estratégia: API REST Interna

Os dois projetos se comunicam via **API REST protegida por API Key**. O bot-zap nunca acessa o banco de dados do Controle_Estoque diretamente.

Vantagens:
- Projetos 100% desacoplados
- Cada um escala independentemente
- Segurança por API Key sem acesso direto ao banco
- Deploys independentes
- Respeita as regras do `Agents.md` (sem SQL livre para o modelo)

---

## Endpoints Disponíveis

Todos os endpoints usam o prefixo `/api/bot/` e requerem o header `X-Bot-Api-Key`.

### Products

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/products?search={termo}` | Busca produtos ativos por nome/código |
| GET | `/api/bot/products/{id}` | Detalhes de um produto |

**Resposta de busca:**
```json
{
  "products": [
    {
      "id": 1,
      "code": "TOM001",
      "name": "Tomate Italiano",
      "description": "Tomate italiano orgânico",
      "unit": "KG",
      "categories": ["Hortifruti"],
      "brands": ["Orgânicos do Vale"]
    }
  ],
  "count": 1
}
```

### Stock

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/stock?product_id={id}` | Estoque de um produto específico |
| GET | `/api/bot/stock/availability?search={termo}` | Produtos disponíveis por termo |

**Resposta de estoque:**
```json
{
  "stock": [
    {
      "product_name": "Tomate Italiano",
      "product_code": "TOM001",
      "quantity": 150.0,
      "sell_price": 12.50,
      "brand": "Orgânicos do Vale",
      "batch": "L2026-03",
      "location": "Câmara Fria A",
      "expiry_date": "2026-04-15"
    }
  ],
  "total_quantity": 150.0
}
```

**Nota:** O `preco_custo` (preço de custo) NÃO é exposto na API — apenas `sell_price`.

### Customers

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/customers?phone={telefone}` | Busca cliente por telefone/WhatsApp |
| GET | `/api/bot/customers/{id}/summary` | Resumo do cliente (compras, saldo) |

**Resposta de busca por telefone:**
```json
{
  "found": true,
  "customer": {
    "id": 1,
    "name": "João Silva",
    "document": "12345678900",
    "whatsapp": "5511999999999",
    "email": "joao@email.com",
    "city": "São Paulo",
    "state": "SP",
    "segment": "Varejo",
    "credit_limit": 5000.00,
    "blocked": false
  }
}
```

### Orders (Vendas/Pedidos)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/orders?customer_cpf={cpf}` | Vendas recentes por CPF/CNPJ |
| GET | `/api/bot/orders/{id}` | Detalhes de um pedido (order) |

### Finance (Financeiro)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/finance/customer-balance?cpf={cpf}` | Saldo financeiro do cliente |

### PriceTables (Tabelas de Preço)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/bot/price-tables/active` | Tabela de preço ativa |
| GET | `/api/bot/price-tables/quote?items=[...]` | Cotação para lista de itens |

**Formato do parâmetro `items` para cotação:**
```json
[
  {"product_id": 1, "quantity": 10},
  {"product_id": 5, "quantity": 5}
]
```

---

## Autenticação

Todos os endpoints são protegidos pelo middleware `ValidateBotApiKey`.

O header esperado é:
```
X-Bot-Api-Key: {chave_configurada}
```

### Configuração

**No Controle_Estoque (`.env`):**
```env
BOT_API_KEY=uma_chave_segura_compartilhada
```

**No bot-zap (`.env`):**
```env
CONTROLE_ESTOQUE_API_URL=http://nginx_server
CONTROLE_ESTOQUE_API_KEY=uma_chave_segura_compartilhada
CONTROLE_ESTOQUE_TIMEOUT_MS=3000
```

---

## Arquitetura de Arquivos

### Controle_Estoque (este projeto)

Arquivos criados/modificados:

```
app/Http/Middleware/ValidateBotApiKey.php          # Middleware de autenticação API
app/Http/Kernel.php                                # Registro do alias bot.api.key
app/Providers/ModuleServiceProvider.php            # Carregamento de api_bot.php
config/services.php                                # Config bot_api.key

Modules/Products/Http/Controllers/BotProductController.php
Modules/Products/Routes/api_bot.php

Modules/Stock/Http/Controllers/BotStockController.php
Modules/Stock/Routes/api_bot.php

Modules/Customers/Http/Controllers/BotCustomerController.php
Modules/Customers/Routes/api_bot.php

Modules/Sales/Http/Controllers/BotOrderController.php
Modules/Sales/Routes/api_bot.php

Modules/Finance/Http/Controllers/BotFinanceController.php
Modules/Finance/Routes/api_bot.php

Modules/PriceTables/Http/Controllers/BotPriceTableController.php
Modules/PriceTables/Routes/api_bot.php
```

### bot-zap (projeto parceiro — implementação futura)

Arquivos a criar no bot-zap:

```
modules/whatsapp/backend/src/Application/Services/Ai/EstoqueApiClient.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/SearchProductsTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/GetProductStockTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/GetProductPriceTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/GetCustomerOrdersTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/GetCustomerBalanceTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Tools/GetStockAvailabilityTool.php
modules/whatsapp/backend/src/Application/Services/Ai/Agents/StockAgent.php
```

---

## Configuração Docker (Rede Compartilhada)

Para que o bot-zap acesse a API do Controle_Estoque:

**Controle_Estoque `docker-compose.yml` — dar nome fixo à rede:**
```yaml
networks:
  laravel-network:
    driver: bridge
    name: shared-network
```

**bot-zap `docker-compose.dev.yml` — usar rede externa:**
```yaml
networks:
  laravel-net:
    external: true
    name: shared-network
```

Com isso, o container do bot-zap acessa `http://nginx_server/api/bot/...` pela rede Docker.

---

## Dados Sensíveis NÃO Expostos

Os seguintes dados **nunca** são retornados pela API:
- `preco_custo` (preço de custo / margem)
- `imposto_total` / `impostos_json` (dados fiscais internos)
- `id_users_fk` (IDs de usuários internos)
- Senhas, tokens, credenciais

---

## Segurança

1. **API Key:** Toda requisição exige header `X-Bot-Api-Key` válido
2. **Comparação timing-safe:** Usa `hash_equals()` para evitar timing attacks
3. **Rate limiting:** Middleware `ThrottleRequests` do grupo `api` já está ativo
4. **Somente leitura:** A API bot é 100% GET — não permite criar, editar ou deletar dados
5. **Dados mínimos:** Cada endpoint retorna apenas o necessário para o agente IA

---

## Fluxo de Exemplo

1. Cliente envia no WhatsApp: *"Tem tomate disponível?"*
2. bot-zap processa e aciona o agente IA
3. Gemini identifica a intenção e chama a tool `search_products("tomate")`
4. A tool faz `GET http://nginx_server/api/bot/stock/availability?search=tomate`
5. Controle_Estoque responde: `{"available": [{"product_name": "Tomate Italiano", "quantity": 150, ...}]}`
6. Gemini formula a resposta: *"Sim! Temos Tomate Italiano disponível (150 KG) por R$ 12,50/kg"*
7. bot-zap envia a resposta pelo WhatsApp

---

## Manutenção

- Para adicionar novos endpoints: crie o controller em `Modules/<Modulo>/Http/Controllers/Bot<Modulo>Controller.php` e registre a rota em `Modules/<Modulo>/Routes/api_bot.php`
- O `ModuleServiceProvider` carrega automaticamente arquivos `api_bot.php` de todos os módulos
- Sempre seguir o padrão: sem dados sensíveis, retorno mínimo, somente leitura
