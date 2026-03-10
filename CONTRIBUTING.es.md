# Cómo Contribuir a Controle de Estoque

¡Estamos encantados de que tengas interés en contribuir! Por favor sigue las siguientes pautas para mantener el código limpio, testeable y adhiriéndose a los patrones arquitectónicos del proyecto.

---

## Paso 1: Configuración del Entorno

Nuestro entorno está 100% en Docker. Consulta el [README.es.md](../README.es.md#cómo-ejecutar) principal para encender los contenedores. Nunca instales paquetes de Composer o NPM directamente en tu máquina host (Windows/Mac); hazlo siempre desde dentro del contenedor `controle_estoque_app`.

## Paso 2: Patrones de Código

Antes de abrir un *Pull Request* (PR), asegúrate de que tu código respete las tres reglas de oro del proyecto:

1. **La Regla de Oro del Controller:** Los Controllers nunca deben tener validación de Request (`$request->validate()`) ni reglas de negocio intrincadas. Utiliza `[Model]Request` para validar y `[Model]Service` para procesar las acciones intensivas.
2. **La Regla de Oro de la Vista (View):** Usamos Inertia.js (Vue 3). Envía todos los datos necesarios a través de un Prop desde el Controller y recíbelo en la configuración (setup) de Vue. Evita hacer llamadas Axios secundarias en el instante exacto que la pantalla carga.
3. **Mantente en tu área:** Si estás creando una funcionalidad financiera, no va en `app/`. Pertenece estrictamente a `src/Modules/Finance/`.

Lee más acerca de los patrones del proyecto en [PATTERNS.es.md](docs/PATTERNS.es.md) y [ARCHITECTURE.es.md](docs/ARCHITECTURE.es.md).

## Paso 3: Ramificación (Branching)

Crea ramas casi siempre a partir de la rama `main`. Usa prefijos de convenciones estándar y separa el nombre del ticket/issue con una corta descripción:

- `feat/nombre-de-la-funcionalidad`
- `fix/nombre-del-bug`
- `refactor/que-se-ha-refactorizado`
- `docs/actualizando-readme`

Por ejemplo: `git checkout -b feat/exportar-excel-stock`

## Paso 4: Pruebas Obligatorias (Tests)

**No aceptamos *Pull Requests* de nuevas funcionalidades sin pruebas.**

Cuando crees o alteres un *Service* o un *Controller*, debes crear la prueba correspondiente (Feature Test HTTP o Test Unitario).

Para correr tu prueba aislada:
```bash
docker exec -it controle_estoque_app php artisan test --filter NombreDeTuTest
```

## Paso 5: Estrategia de CI (Integración Continua)

GitHub Actions se encarga de ejecutar automáticamente todas las pruebas (PHPUnit) usando una base de datos en la memoria o SQLite tras cada *Push* y *Pull Request* rumbo a la `main`. 
Si tu PR rompe la prueba en el CI (se marcará con una "X" roja), no podrá ser emergido (merged). Por ello, compruébalo localmente con el comando de pruebas mencionado.

## Paso 6: Entregando tu PR

1. Sube tus alteraciones para tu *fork* / repositorio origen y abre un nuevo *Pull Request* comparando tu rama en contra de `main`.
2. Incluye en la descripción del PR el problema específico que está siendo resulto.
3. Si el PR envuelve alteraciones visuales y de frontend (Inertia/Vue/Tailwind), añade **screenshots** en los comentarios del PR.

---

¡Gracias por ayudar a que el **Controle de Estoque** sea más asombroso cada día! 🚀
