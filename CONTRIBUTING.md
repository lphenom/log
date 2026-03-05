# Contributing to lphenom/log

Thank you for your interest in contributing! 🎉

## Ground Rules

- PHP >= 8.1, strict types in every file.
- KPHP-compatible: no `Reflection`, no `eval`, no `variable variables`, no dynamic class loading.
- Follow PSR-12 coding style (enforced by PHP CS Fixer + PHPCS).
- All public API changes must be backward-compatible or bump the minor/major version.
- Every new feature must come with unit tests.

## Development Setup

```bash
# Clone the repo
git clone git@github.com:lphenom/log.git
cd log

# Build & start Docker environment
make build
make install

# Run tests
make test

# Run linter (dry-run)
make lint

# Auto-fix code style
make lint-fix

# PHPStan static analysis
make phpstan
```

## Branching & Commits

- Work on a feature branch: `feat/your-feature` or `fix/your-fix`.
- Commits must be small and focused (one logical change per commit).
- Use [Conventional Commits](https://www.conventionalcommits.org/) format:
  - `feat(log): add X`
  - `fix(log): correct Y`
  - `test(log): add tests for Z`
  - `docs(log): update README`
  - `chore: update dependencies`

## Pull Request Process

1. Fork the repository.
2. Create your feature branch.
3. Write tests for your changes.
4. Ensure `make test`, `make lint`, and `make phpstan` all pass.
5. Open a Pull Request against `main`.
6. A maintainer will review and merge.

## Code Style

We use **PHP CS Fixer** with PSR-12 + strict types. Run `make lint-fix` before committing.

## Questions?

Open a GitHub Discussion or email popkovd.o@yandex.ru.

