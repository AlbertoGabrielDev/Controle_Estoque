# Modules

Raiz da arquitetura por modulo (feature modules).

Padrao arquitetural (alvo):
- `Controller` e obrigatorio para entradas HTTP.
- `FormRequest` e obrigatorio quando houver entrada de dados.
- `Service` e obrigatorio para regra de negocio/orquestracao/transacoes.
- `Repository` + `RepositoryEloquent` sao condicionais (consulta complexa, multiplas fontes, mocks/fakes).
- `Model` fica com relacoes/casts/scopes e sem regra de negocio pesada.
- `Jobs/Events/Listeners/Commands` so quando o modulo realmente usa.

Estrutura padrao de pasta (nem toda pasta precisa ser usada):
- `Modules/<Modulo>/Http/{Controllers,Requests}`
- `Modules/<Modulo>/{Services,Models,Contracts,Repositories}`
- `Modules/<Modulo>/Repositories/Contracts`
- `Modules/<Modulo>/Database/{Migrations,Seeders,Factories}`
- `Modules/<Modulo>/{Jobs,Console/Commands,Events,Listeners,Observers}`
- `Modules/<Modulo>/{Policies,Notifications,Rules,DTOs,Actions,Gateways,Broadcast}`
- `Modules/<Modulo>/Resources/{js,views}`
- `Modules/<Modulo>/Routes`
- `Modules/<Modulo>/Tests/{Feature,Unit}`

Observacoes:
- As migrations/seeders/factories ficam dentro do modulo.
- O provider `App\\Providers\\ModuleServiceProvider` descobre modulos automaticamente.
- O `phpunit.xml` inclui `Modules/` no source e suite de testes modulares.
- O modulo piloto inicial criado nesta fase foi `Products`.
