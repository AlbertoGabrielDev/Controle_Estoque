# Planejamento do Módulo Multi-idiomas (i18n)

Este documento descreve o plano para implementar suporte a múltiplos idiomas no projeto de Controle de Estoque, utilizando Laravel no backend e Vue 3 (Inertia) no frontend.

## Objetivo
Adicionar suporte aos seguintes idiomas:
- **Português (Brasil)**: `pt_BR` (Padrão)
- **Inglês**: `en`
- **Espanhol (Espanha)**: `es`

## Tecnologias Propostas
- **Backend**: Middleware do Laravel para gestão de locale.
- **Frontend**: Biblioteca `laravel-vue-i18n` para integração transparente entre as traduções do Laravel e o Vue.

## Etapas da Implementação

### 1. Configuração do Backend (Laravel)
- **Criação dos arquivos de tradução**: 
  - Criar a pasta `src/lang` se não existir.
  - Criar arquivos JSON: `en.json`, `pt_BR.json`, `es.json`.
- **Middleware de Localização**:
  - Criar `SetLocaleMiddleware` para ler o idioma escolhido da sessão ou cookies.
  - Registrar no grupo `web` em `Kernel.php`.
- **Configuração Global**:
  - Atualizar `config/app.php` para definir `pt_BR` como idioma principal.

### 2. Configuração do Frontend (Vue 3)
- **Instalação**: Adicionar `laravel-vue-i18n` via npm.
- **Vite Config**: Configurar o plugin para que as traduções fiquem disponíveis no frontend automaticamente.
- **App Init**: Inicializar o plugin i18n no `app.js`.

### 3. Componentes de Interface
- **Seletor de Idioma**: Criar um componente `LanguageSwitcher.vue` que permite ao usuário alternar entre as 3 opções.
- **Tradução da UI**: Substituir textos estáticos por chaves de tradução (ex: `{{ $t('Dashboard') }}`).

### 4. Persistência
- Garantir que a escolha do usuário seja salva na sessão para que o idioma se mantenha ao navegar pelas páginas.

## Plano de Verificação
- **Testes Manuais**: Verificar a troca de idioma no dashboard e em um formulário de exemplo.
- **Testes Automatizados**: Criar um teste de funcionalidade (Feature Test) para validar a rota de troca de idioma.
