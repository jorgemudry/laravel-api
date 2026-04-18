# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Purpose

An API-oriented Laravel 13 starter template (PHP 8.5). Ships with a custom JSON exception pipeline, Prometheus metrics, route versioning, and a pre-commit command that lints/analyses/tests staged PHP files. There is no frontend; the `api` middleware group forces JSON responses on every route.

## Common Commands

- **Run tests**: `php artisan test --compact` (or `composer test`, which clears config first). Filter with `--filter=testName`.
- **Single Pest run with parallelism** (matches pre-commit): `./vendor/bin/pest --parallel`.
- **Static analysis**: `vendor/bin/phpstan` (level 8, scans `app/ tests/ routes/ database/ config/`).
- **Formatter**: `vendor/bin/pint --format agent` (full) or `vendor/bin/pint --dirty --format agent` (changed files only). Never use `--test`.
- **Local dev stack** (server + queue worker + `pail` logs + vite): `composer dev`.
- **Pre-commit gate** (lint → Pint → PHPStan → Pest on staged `.php` files): `php artisan dev:precommit`.
- **Install git hooks** (pre-commit + post-merge): `./dev/hooks/install`. Runs automatically via the composer `post-install-cmd`.

Test DB defaults to SQLite in-memory via `phpunit.xml` env vars. `RefreshDatabase` is not enabled globally in `tests/Pest.php` — opt in per test if you touch the DB.

## Architecture

### Request entry & routing
- `bootstrap/app.php` registers **only** the `api` route file with an empty `apiPrefix`, so every route is API-first.
- `routes/api.php` delegates to `routes/v1/api.php` under prefix `v1` and name prefix `v1:`.
- `routes/v1/api.php` further groups `service/*` (health) and `metrics/*` (Prometheus) into their own files.
- The `api` middleware group applies: `ForceJsonResponse` → `ThrottleRequests:api` → `SubstituteBindings` → public cache headers. The `api` rate limiter is defined in `AppServiceProvider::boot` (60/min, keyed by `user()->id` or IP). The commented `TreblleMiddleware` and `ThrottleRequestsWithRedis` lines show the intended opt-in swaps.

### Exception pipeline (JSON-only)
`bootstrap/app.php` forces JSON rendering for *all* exceptions via this chain:

1. `FlattenException::createFromThrowable($e)` — extended Symfony flatten exception that also retains the `$original` throwable.
2. `ExceptionMapper::fromThrowable($e)->map(...)` — maps `ModelNotFoundException`/`AuthorizationException`/`AuthenticationException`/`ValidationException` to the correct HTTP status and sets a default message when empty.
3. `ErrorResponseBuilder::fromFlatten(...)->build(debug: config('app.debug'))` — injects an `x-app-debug` header.
4. `ExceptionResource` — final JSON shape: `{status, type, error}`, adds `fields` for validation errors, adds `file/line/trace` when debug is on. `withResponse()` unwraps the resource `data` envelope so the body is flat.

When adding a new exception type, extend `Symfony\Component\HttpKernel\Exception\HttpException` (enforced by `tests/Arch/ArchitectureTest.php`) — unless it's one of the three helper classes in `App\Exceptions` that are whitelisted. If a new core exception should map to a specific HTTP status, add it to `ExceptionMapper::setStatusCode()`.

### V1 controllers
All `App\Http\Controllers\V1\*` classes **must be invokable** (single `__invoke`) — enforced by an arch test. Current controllers: `ServiceAliveController`, `ServiceReadyController`, `PrometheusMetricsController`.

### Prometheus
`App\Prometheus\Prom` is a Facade over `Prometheus\CollectorRegistry`. The controller renders metrics with `RenderTextFormat`. Note the `Prom::fake()` stub is currently a no-op placeholder.

### API response envelope
Use `App\Traits\HasApiResponse::response(JsonResource)` when returning resource responses — it adds a `metadata: {code, message}` block and renames paginator `meta` → `pagination` (dropping `meta.links` to keep payloads small). Follow this shape for any new paged endpoints.

### Models
- `App\Models\UnGuardedModel` is the shared base: `HasUlids` + `$guarded = []`. New models that want ULID PKs and unguarded mass assignment should extend it instead of `Illuminate\Database\Eloquent\Model`.
- `Model::shouldBeStrict()` is enabled outside production (`AppServiceProvider`), so lazy-loading and missing-attribute access will throw in local/test — fix the N+1 rather than silencing it.

### Enums
Every enum in `App\Enums` must implement `App\Contracts\ToArrayEnum` (enforced by arch test). Use the `App\Traits\HasToArrayEnum` trait for the default implementation, TitleCase keys.

### Architecture tests (`tests/Arch/ArchitectureTest.php`)
These run as part of the normal suite and enforce: `strict_types` on all `App/*`, invokable V1 controllers, `ToArrayEnum` contract, no debug functions (`dd`, `dump`, `ray`, `var_dump`, `print_r`) anywhere, and `HttpException` base class for app exceptions (with the three helper whitelist). Update this file when you add architectural rules — don't add ad-hoc rules elsewhere.

## Conventions

- `declare(strict_types=1);` is required on every PHP file in `app/` (arch test enforces it).
- Pint config (`pint.json`) extends the `laravel` preset and additionally enforces: strict comparisons (`===`), arrow functions, `mb_*` string helpers, sorted imports, fully qualified strict types, and global namespace imports (classes/constants/functions).
- PHPStan runs at **level 8** with `checkMissingTypehints: true`. Don't suppress — add proper types.
- Pre-commit `php artisan dev:precommit` auto-runs Pint against staged files and re-`git add`s them, so formatting failures self-heal on commit.
- `.github/workflows/composer.yml` runs `composer update && composer bump` on a weekly cron and commits `composer.json`/`composer.lock` as `GitHub Action` — expect periodic dependency bumps on `master`.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `pest-testing` — Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: test()/it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
