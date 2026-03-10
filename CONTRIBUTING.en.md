# How to Contribute to Controle de Estoque

We are delighted that you are interested in contributing! Please follow the guidelines below to keep the code clean, testable, and strictly obedient to the project's architectural patterns.

---

## Step 1: Environment Setup

Our environment is 100% Dockerized. See the main [README.en.md](../README.en.md#getting-started) to spin up the container. Never install Composer or NPM packages directly on your host machine (Windows/Mac); always do so from inside the `controle_estoque_app` container.

## Step 2: Coding Standards

Before opening a *Pull Request* (PR), ensure your code respects the three golden rules of the project:

1. **The Golden Rule of the Controller:** Controllers should never execute Request validation (`$request->validate()`) or complex business rules. Use `[Model]Request` to validate and `[Model]Service` to process intensive actions.
2. **The Golden Rule of the View:** We use Inertia.js (Vue 3). Pass all necessary data via Props from the Controller and catch it in the Vue setup script. Avoid making secondary Axios calls right when the screen loads.
3. **Stay in your lane:** If you are creating a financial feature, it does not belong in `app/`. It strictly belongs in `src/Modules/Finance/`.

Read more about the project's design patterns in [PATTERNS.en.md](docs/PATTERNS.en.md) and [ARCHITECTURE.en.md](docs/ARCHITECTURE.en.md).

## Step 3: Branching

Always branch off from `main`. Use standard conventional branch prefixes and separate the task/issue name with a short description:

- `feat/feature-name`
- `fix/bug-name`
- `refactor/what-was-refactored`
- `docs/updating-readme`

Example: `git checkout -b feat/stock-excel-export`

## Step 4: Mandatory Tests

**We do not accept feature *Pull Requests* without tests.**

When adding or modifying a *Service* or a *Controller*, you must create the corresponding tests (HTTP Feature Test or Unit Test).

To run your specific test file:
```bash
docker exec -it controle_estoque_app php artisan test --filter YourTestName
```

## Step 5: CI Approach

GitHub Actions automatically runs all PHPUnit tests using an in-memory SQLite database on every push and pull request towards `main`.
If your PR breaks the CI build (shows a red "X"), it will not be merged. Verify locally first using your exact test command.

## Step 6: Submitting the PR

1. Push the changes to your *fork* / origin repository and open a new *Pull Request* comparing your branch against `main`.
2. Include in the PR description exactly what issue is being solved.
3. If the PR involves frontend or visual changes (Inertia/Vue/Tailwind), provide **screenshots** in the PR comments.

---

Thank you for helping us make **Controle de Estoque** even better! 🚀
