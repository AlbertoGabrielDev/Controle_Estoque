# Modules

Raiz da arquitetura por modulo (feature modules).

Padrao inicial:
- `Modules/<Modulo>/Http`
- `Modules/<Modulo>/Services`
- `Modules/<Modulo>/Repositories`
- `Modules/<Modulo>/Models`
- `Modules/<Modulo>/Database/{Migrations,Seeders,Factories}`
- `Modules/<Modulo>/Resources/js`
- `Modules/<Modulo>/Resources/views`
- `Modules/<Modulo>/Routes`

Observacoes:
- As migrations/seeders/factories ficam dentro do modulo.
- O provider `App\\Providers\\ModuleServiceProvider` descobre modulos automaticamente.
- O modulo piloto inicial criado nesta fase e `Products`.
