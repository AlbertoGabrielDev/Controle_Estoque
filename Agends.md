# Project Guidance (AGENTS.md)

## Stack
- Docker, Laravel, Inertia, Vue 3, MySQL, NodeJS (quando necessário)
- Front: Vue 3 Composition API, páginas em resources/js/Pages
- Back: Controllers + FormRequests + Eloquent

## Rules
- Preferir CRUDs completos (index/create/store/edit/update/destroy) e fazer uso do Repository e Services
- Validação sempre em FormRequest
- Não introduzir libs novas sem necessidade
- Respostas com correção de código devem entregar o arquivo completo alterado
- UI simples e consistente (tabela + filtros + paginação + formulário)
- Usar PHPUnit no docker, com sqlLite
- Caso haja button de editar ou de status usar os componentes criados de Edit e Status
- Sempre que for criar uma tabela usar o componente DataTable
- Respeitar a estrutura de pastas e arquivos do projeto
- Respeitar a modulação do projeto
- Respeitar a arquitetura do projeto
- Lembrar de usar caso necessário os componentes de UI criados  
