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

## Estratégia: API REST Interna Modular

Os endpoints foram estruturados seguindo uma padronização modular a fim de unificar as responses e fornecer mais flexibilidade via filtros.
A classe `BaseBotController` padroniza os formatos de moeda e respostas HTTP, garantindo que as respostas sejam consistentes em todos os módulos.
Buscas por texto (`search`) são dinâmicas e cruzam dados de nomes, códigos e categorias.

Vantagens:
- Projetos 100% desacoplados
- Busca Semântica Centralizada (Categorias e Pistas Semânticas como "fruta" -> "maçã", "uva")
- Segurança por API Key (Preparada para Multi-Tenant futuro)
- Respeita as regras de Agents (sem SQL livre para o modelo da IA)

---

## Endpoints Disponíveis

Todos os endpoints usam o prefixo `/api/bot/` e requerem o header `X-Bot-Api-Key`.

### Produtos (`Products`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/products` | `search`, `category_id`, `brand_id`, `code`, `limit` | Busca produtos ativos |
| GET | `/api/bot/products/{id}` | - | Detalhes de um produto |

### Estoque (`Stock`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/stock` | `search`, `product_id`, `batch`, `min_quantity`, `limit` | Busca estoque disponível |
| GET | `/api/bot/stock/{id}` | - | Detalhes de um item de estoque |

**Nota:** O `preco_custo` (preço de custo) NÃO é exposto na API — apenas `sell_price`.

### Clientes (`Customers`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/customers` | `phone`, `document`, `name`, `email`, `limit` | Busca cliente |
| GET | `/api/bot/customers/{id}` | - | Detalhes do cliente |
| GET | `/api/bot/customers/{id}/summary` | - | Resumo do cliente (compras, saldo) |

### Vendas e Pedidos (`Sales`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/orders` | `customer_cpf`, `customer_id`, `limit` | Vendas recentes por cliente |
| GET | `/api/bot/orders/{id}` | - | Detalhes e itens de um pedido |

### Financeiro (`Finance`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/finance` | `customer_cpf`, `customer_id` | Saldo financeiro e totais recentes do cliente |

### Tabelas de Preço (`PriceTables`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/price-tables` | - | Retorna a tabela ativa e os produtos no escopo |
| GET | `/api/bot/price-tables/quote` | `items=[{"product_id": 1, "quantity": 10}]` | Cotação para lista de itens baseada na tabela ativa |

### Categorias (`Categories`)

| Método | Rota | Filtros Aceitos | Descrição |
|--------|------|-----------------|-----------|
| GET | `/api/bot/categories` | `search`, `limit` | Lista categorias de produtos ativas |
| GET | `/api/bot/categories/{id}` | - | Detalhes de uma categoria |

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

## Estrutura Arquitetural da API

A estrutura modular da API Bot segue uma arquitetura baseada no `BaseBotController` e nos `api_bot.php` de cada módulo:

```text
app/Http/Controllers/Bot/BaseBotController.php     # Controller base com response formatter
app/Http/Middleware/ValidateBotApiKey.php          # Middleware de autenticação API
app/Http/Kernel.php                                # Registro do alias bot.api.key
app/Providers/ModuleServiceProvider.php            # Carregamento automático de api_bot.php
config/services.php                                # Config bot_api.key

Modules/[Nome_Modulo]/Http/Controllers/Bot[Nome_Modulo]Controller.php
Modules/[Nome_Modulo]/Routes/api_bot.php
```

---

## Dados Sensíveis NÃO Expostos

Para garantir a segurança dos dados da loja, os seguintes dados **nunca** são retornados pela API:
- `preco_custo` (preço de custo / margem)
- `imposto_total` / `impostos_json` (dados fiscais internos)
- `id_users_fk` (IDs de usuários internos)
- Senhas, tokens, credenciais

---

## Segurança

1. **API Key:** Toda requisição exige header `X-Bot-Api-Key` válido
2. **Comparação timing-safe:** Usa `hash_equals()` para evitar timing attacks
3. **Somente leitura:** A API bot é 100% GET — não permite criar, editar ou deletar dados
4. **Respostas padronizadas:** Baseadas no `BaseBotController` ocultando detalhes indesejados

---

---

## Swagger / OpenAPI

Para uma visualização interativa de todos os endpoints, parâmetros e schemas de resposta, utilize o arquivo de especificação técnica:

- **Arquivo:** [bot_api_swagger.yaml](file:///c:/Users/Alberto%20Gabriel/Documents/Projetos/Controle_Estoque/docs/bot_api_swagger.yaml)

### Como visualizar:
1. Acesse o [Swagger Editor](https://editor.swagger.io/).
2. Vá em `File` -> `Import File`.
3. Selecione o arquivo `docs/bot_api_swagger.yaml`.
4. Você terá uma interface interativa para testar e entender a estrutura da API.

Fim do documento.
