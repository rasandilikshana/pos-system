# POS System — Project Conventions

These rules apply to every change in this project.

## Conventions (project rules)

### PHP

- PHP 8.4 constructor property promotion. Never empty zero-parameter `__construct()` unless the constructor is private.
- Explicit return types and parameter type hints on every method. No `mixed` unless unavoidable.
- TitleCase Enum keys: `Active`, `Suspended`, `Monthly`.
- PHPDoc array shape definitions for complex array params: `@param array{author_id:string,title:string,price:int} $attrs`.
- No inline `//` comments describing what a piece of code does. No `{{-- blade comments --}}`.
- Comments only on functions and classes for what they do and what they expect — never describing a step inside.

### Models

- `$fillable` explicit on every model. Never `$guarded = []`.
- `$casts` for every non-string column (`'decimal:2'`, `'boolean'`, `'datetime'`, enum, array).
- Traits in this order: `HasFactory, HasUuids, SoftDeletes, LogsActivity` (whichever apply).
- Relationships return typed: `BelongsTo`, `HasMany`, `BelongsToMany`, `HasOne`.
- Scopes via `#[Scope]` attribute (Laravel 11+), not `scopeXxx()`.
- `getActivitylogOptions()` returns `LogOptions::defaults()->logOnly([...])->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('...')`.
- What does NOT belong in models: HTTP, validation, business logic spanning multiple models.

### Migrations

- `uuid('id')->primary()` for distribution-friendly IDs.
- `foreignUuid('x_id')->constrained('xs')->{restrict|cascade|null}OnDelete()` — pick deliberately.
- `softDeletes()` on records that carry history.
- `index()` on every column you `where`/`orderBy` at scale.
- Composite indexes for combined filters.
- `unique()` for natural keys (ISBN, email, code).
- Always write a working `down()` — never leave migrations irreversible.

### Repositories

- Every repository extends `BaseRepository` and implements `RepositoryInterface`. No raw `DB::table()` repositories.
- Method vocabulary: `create`, `update`, `delete`, `find*`, `paginate`, `list`, `exists`.
- Repositories return Eloquent types: `Model`, `Collection`, `LengthAwarePaginator`, `Builder` — never untyped `object` or raw `array` of rows.
- Always eager-load relations inside repository methods (`->with([...])`) to prevent N+1.
- `findOrFail` not `find` — fail loudly on missing records.
- Return `->fresh()` after writes so callers see DB-truth.
- Use constructor property promotion for any dependencies.

### Services

#### File order

1. Constructor with promoted properties (PHP 8.4)
2. Public CRUD: `create` → `update` → `delete` → `restore` (if soft-deletes)
3. Public reads: `find($id)` → `findBy*` → `paginate` → `list` (Collection) → `exists`
4. Public domain actions (verbs: `approve`, `activate`, `sync*`)
5. Protected/private helpers last

#### Naming

- Canonical verbs: `create`, `update`, `delete`, `find*`, `paginate`, `list`, `exists`.
- `save($data, ?$id)` dual-purpose is retired; split into `create` + `update` on touch.
- `get*` is reserved for simple accessors returning computed values (`getDefaultCurrency()`). DB-backed queries use `find*` / `list` / `paginate`.
- Forbidden verbs: `fetch*`, `store*`, `make*`.
- Stateless utility services (`PermissionService`, `WebhookSigner`) remain static — do not convert to instance.

### Transaction discipline

- Any service or action public method that writes to 2+ tables, deletes with cascading side-effects, or runs a loop of writes MUST wrap in `DB::transaction(fn () => ...)`.
- Single-table single-row writes do NOT need a transaction (Eloquent handles it).
- The transaction boundary lives at the **SERVICE / ACTION** layer — not inside repositories, not inside Livewire components.
- Every transactional wrap must have a test covering the rollback path (force mid-transaction failure, assert nothing persisted).

### Livewire components

#### File order

1. `use` traits
2. Form objects (`public LoginForm $form;`)
3. Public primitive / model properties, grouped: identity → UI state → filters → pagination
4. Protected properties (`$listeners`, `$queryString`)
5. Lifecycle: `mount` → `boot` → `hydrate` → `updating*` → `updated*`
6. `#[Computed]` properties
7. `#[On(...)]` event listener methods
8. Public actions (verbs: `save`, `delete`, `toggleFoo`)
9. Protected/private helpers
10. `render()` last

#### Validation

- A component that accepts `wire:model` input MUST use a Form object under `app/Livewire/Forms/`.
- Inline `$this->validate([...])` is permitted ONLY for single-field confirmation modals.
- The `$rules` array property is deprecated; remove on touch.

#### Authorization

- Always `$this->authorize('perm.verb')` in `mount()` before any data load.
- Route-level `->middleware('can:perm.verb')` is the coarse gate.
- `@can` in Blade for hiding UI elements.

### Routes

- Group by auth / permission.
- Named routes (`->name(...)`) always — `route('books.edit', $book)`, never hardcoded paths.
- Constrain UUID parameters: `->whereUuid('id')`.
- For Livewire pages: `Route::livewire('/x', X\Index::class)->name('x.index')`.
- For controllers (PDFs, CSVs, webhooks): `Route::get('/x/export', [XController::class, 'export'])`.

### Async work

- Jobs declare `public int $tries = 3;` and `public int $backoff = 60;`.
- Anything >200ms or non-essential to the response → push to queue.
- Mail classes use `--markdown=emails.X.Y` template.
- Scheduled commands wired in `routes/console.php` via `Schedule::command('...')->dailyAt('...')`.

### Tests

- Every change must have a programmatic test. Either a new test or an existing one updated.
- Most tests are Feature tests, not Unit. Use `php artisan make:test --pest XTest`.
- Use `RefreshDatabase`, factories, and `actingAs($user)` for auth.
- For Livewire: `Livewire::actingAs($u)->test(Component::class)->set('form.x', 'y')->call('save')->assertHasNoErrors()`.
- Coverage minimum per feature: happy path + at least one authorization failure case.

### Localization

- Every user-visible string wrapped in `__('...')`.
- New strings added ONLY to `lang/texts_to_translate.json`. Never edit `english.json`, `spanish.json` directly — those are manually translated.
- Cross-check `english.json` and remove keys that already exist from `texts_to_translate.json`.

### Formatting

- After modifying any PHP file: `vendor/bin/pint --dirty --format agent` before committing.
- After modifying any `*.blade.php` file: `npx prettier --write "<path>"`.
- Do NOT run `vendor/bin/pint --test`; just run `vendor/bin/pint --format agent` to fix.

### Commit message templates

```
chore: scaffold Laravel <ver> + Livewire <ver> + Flux Pro + <deps>
feat(<app>): schema + models + seeders with UUID PKs/FKs across all tables
feat(<app>): <Service> + DTOs (...core algorithm...)
feat(<app>): <API name> + Sanctum + idempotency + <special-case>
feat(<app>): Flux Pro layout shell + Livewire login + dashboard skeleton
feat(<app>): admin UI — <Module1>/<Module2>/.. Index+Edit; sidebar wired; N/N Pest pass
refactor(<app>): adopt Laravel Boost best practices — Repository + Actions + DI; N/N Pest pass
feat(<app>): <Module> — <one-line scope>; N/N Pest pass
refactor(<app>): Form objects + Create/Update action split per CLAUDE.md
refactor(<app>): index tables — <Canon>-canonical Flux pattern across all N
style(<app>): normalize <thing> per uniformity; strip comments; pint format
feat(<feature>): add <thing> + update related actions and forms
```

### Pre-commit checklist

Run before every commit:

- [ ] `php artisan test --compact` → all green
- [ ] `vendor/bin/pint --dirty --format agent`
- [ ] `npx prettier --write "<changed-blade-files>"`
- [ ] `git diff` reviewed — no debug code, no commented-out code
- [ ] `git status` clean — no stray files
- [ ] No new `//` inline comments, no `{{-- blade --}}` comments
- [ ] All new user-facing strings wrapped in `__('...')` and added to `lang/texts_to_translate.json`
- [ ] Any new migration has a working `down()`
- [ ] Authorization checked in `mount()` or route middleware for any new Livewire component
- [ ] No N+1 (repos eager-load via `->with([...])`)
