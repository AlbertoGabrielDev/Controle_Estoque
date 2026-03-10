# Como Contribuir para o Controle de Estoque

Ficamos felizes que você tenha interesse em contribuir! Siga as diretrizes abaixo para manter o código limpo, testável e seguindo os padrões arquiteturais do projeto.

---

## Passo 1: Configuração do Ambiente

Nosso ambiente é 100% Dockerizado. Consulte o [README.md](../README.md#como-rodar-localmente) principal para rodar o contêiner. Nunca instale pacotes Composer ou NPM diretamente no seu host (Windows/Mac); faça isso sempre de dentro do contêiner `controle_estoque_app`.

## Passo 2: Padrões de Código

Antes de abrir um *Pull Request* (PR), certifique-se de que o seu código respeita as três regras de ouro do projeto:

1. **A Regra de Ouro do Controller:** Controllers nunca devem ter validação de Request (`$request->validate()`) ou regras de negócio profundas. Use `[Model]Request` para validar e `[Model]Service` para processar ações pesadas.
2. **A Regra de Ouro do View:** Usamos Inertia.js (Vue 3). Envie todos os dados necessários via Prop pelo Controller e recupere no setup do Vue. Evite chamadas Axios secundárias quando a tela for carregada.
3. **Mantenha-se no seu Quadrado:** Se estiver criando uma funcionalidade financeira, ela não vai em `app/`. Ela pertence estritamente a `src/Modules/Finance/`.

Leia mais sobre os padrões do projeto em [PATTERNS.md](PATTERNS.md) e [ARCHITECTURE.md](ARCHITECTURE.md).

## Passo 3: Criando a Branch

Crie as branches sempre a partir da `main`. Utilize o padrão de prefixos convencionais e separe o nome do ticket/issue com a descrição curta:

- `feat/nome-da-feature`
- `fix/nome-do-bugfix`
- `refactor/o-que-foi-refatorado`
- `docs/atualizacao-readme`

Por exemplo: `git checkout -b feat/exportar-excel-estoque`

## Passo 4: Testes Obrigatórios

**Não aceitamos *Pull Requests* sem testes para novas funcionalidades.**

Quando criar ou alterar um *Service* ou um *Controller*, você deve criar os testes correspondentes (Feature Test HTTP ou Teste Unitário).

Para rodar o seu teste:
```bash
docker exec -it controle_estoque_app php artisan test --filter NomeDoSeuTest
```

## Passo 5: Abordagem de CI (Integração Contínua)

O GitHub Actions roda automaticamente todos os testes (PHPUnit) usando um banco em memória ou SQLite em todo *Push* e *Pull Request* rumo a `main`.
Se o seu PR queimar o CI (aparecer um "X" vermelho), ele não será emergido (merged). Verifique localmente executando seu comando de testes.

## Passo 6: Submetendo o PR

1. Suba as alterações para o seu *fork* / repositório origin e abra um novo *Pull Request* comparando a sua branch contra a `main`.
2. Inclua na descrição do PR qual problema ele resolve.
3. Se o PR envolver alterações visuais (Inertia/Vue/Tailwind), adicione **screenshots** nos comentários do PR.

---

Obrigado por ajudar a tornar o **Controle de Estoque** ainda melhor! 🚀
