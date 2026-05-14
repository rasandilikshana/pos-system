# Architecture — POS System

How this codebase is organized and **where each kind of change goes**.

---

## TL;DR

```
┌─ Presentation (per surface) ─────────────────────────┐
│                                                      │
│  WEB (Livewire)              API (Controllers)       │
│  ┌──────────────────┐        ┌──────────────────┐    │
│  │ Livewire Page    │        │ Controller       │    │
│  │ Form Object      │        │ FormRequest      │    │
│  │ Blade view       │        │ JsonResource     │    │
│  └────────┬─────────┘        └────────┬─────────┘    │
│           │                           │              │
└───────────┼───────────────────────────┼──────────────┘
            │                           │
            ▼                           ▼
┌─ Business (shared, write-once) ──────────────────────┐
│                                                      │
│       Action (DB::transaction boundary)              │
│              │                                       │
│              ▼                                       │
│       Repository (Eloquent queries)                  │
│              │                                       │
│              ▼                                       │
│       Model (schema + casts + relationships)         │
│                                                      │
└──────────────────────────────────────────────────────┘
            │
            ▼
       Database
```

**Rule of thumb:** business rules live in **Actions** and **Repositories**, not in controllers or Livewire components.

---

## The layers — what each one does (and never does)

### 1. Database schema — `database/migrations/`

**Does:** define columns, constraints, indexes, FKs, soft deletes.

**Never:** business rules, defaults that depend on user state.

### 2. Models — `app/Models/`

**Does:**
- Declare `$fillable` (whitelisted mass-assignable columns)
- Declare `$casts` (`'decimal:2'`, `'boolean'`, `'datetime'`)
- Declare relationships with return types: `BelongsTo`, `HasMany`
- Declare `#[Scope]` query helpers (`active`, `lowStock`)
- Configure `LogsActivity` options

**Never:**
- Validate user input (that's the Form / FormRequest)
- Call other services
- Make HTTP calls
- Cross-entity logic (a single model shouldn't know about multiple unrelated entities)

Examples: [`app/Models/Product.php`](app/Models/Product.php), [`app/Models/Sale.php`](app/Models/Sale.php)

### 3. Repositories — `app/Repositories/`

**Does:**
- Encapsulate all Eloquent queries for one entity
- Always eager-load relations via `->with([...])` to prevent N+1
- Return Eloquent types: `Model`, `Collection`, `LengthAwarePaginator`, `Builder`
- Inherit base CRUD from [`BaseRepository`](app/Repositories/BaseRepository.php) via the [`RepositoryInterface`](app/Contracts/RepositoryInterface.php) contract

**Never:**
- Wrap `DB::transaction` (that lives in Actions/Services)
- Send mail / dispatch jobs
- Return arrays of rows (use Eloquent collections)

**Method vocabulary:** `create`, `update`, `delete`, `find*`, `paginate`, `list`, `exists`.
Forbidden verbs: `fetch*`, `store*`, `make*` (collide with framework conventions).

### 4. Actions — `app/Actions/<Module>/`

**Does:**
- ONE business operation per class (one `execute()` method)
- Wrap `DB::transaction(fn () => ...)` when writing to **2+ tables** or running a loop of writes
- Compose Repositories
- Trigger side effects (events, mail, jobs)

**Never:**
- Validate (that's done before the action is called)
- Render HTML / JSON
- Be reused for both create and update — split them when they diverge (`CreateXAction` + `UpdateXAction`). Currently we still have `SaveXAction` from Phase 6; Phase 8 will split them.

Examples: [`app/Actions/Products/SaveProductAction.php`](app/Actions/Products/SaveProductAction.php), [`app/Actions/Users/SaveUserAction.php`](app/Actions/Users/SaveUserAction.php)

### 5. Services — `app/Services/`

**Does:**
- Pure calculations / domain logic with no Eloquent writes
- Take primitive / DTO input, return DTO output
- Are stateless

**Example:** [`app/Services/CartCalculator.php`](app/Services/CartCalculator.php) — given items + discount + tax rate, returns a [`CartTotals`](app/Services/DTO/CartTotals.php) DTO. No DB access. Unit-testable in isolation.

**Never:**
- Read/write the DB directly. If you need DB, accept a Repository.
- Touch HTTP, sessions, or auth.

---

## Presentation layer — web

### 6. Form Objects — `app/Livewire/Forms/`

**Does:**
- Declare form fields as public typed properties with `#[Validate]` attributes
- Implement `rules()` for dynamic / context-aware rules (e.g. unique-ignore-on-edit)
- Implement `setX(?Model $x)` to populate from a model on edit
- Implement `attributes(): array` — runs `validate()` and returns the shaped array for the Action

**Never:**
- Persist to the DB. That's the Action's job.
- Call other forms, controllers, or services that own logic.

Example: [`app/Livewire/Forms/ProductForm.php`](app/Livewire/Forms/ProductForm.php)

### 7. Livewire Components — `app/Livewire/<Module>/`

**Does:**
- Handle HTTP-like state (URL filters via `#[Url]`, pagination via `WithPagination`)
- Inject Repositories via `render(XRepository $repo)`
- Inject Actions via `save(SaveXAction $save)`
- Authorize in `mount()` (`abort_unless(auth()->user()?->can('...'), 403)`)
- Flash session messages, redirect after save

**Never:**
- Run `DB::transaction`
- Call `Model::query()` directly inside `render()` after Phase 6 (use the Repository)
- Send mail or dispatch jobs directly (go through the Action)

Examples: [`app/Livewire/Products/Index.php`](app/Livewire/Products/Index.php), [`app/Livewire/Products/Edit.php`](app/Livewire/Products/Edit.php)

### 8. Blade views — `resources/views/livewire/<module>/`

**Does:** present data using Flux components.

**Never:** `@foreach (Model::all() as $x)` — queries from Blade = N+1 + untestable. Compute in `render()` or `#[Computed]`.

---

## Presentation layer — API

### 9. Form Requests — `app/Http/Requests/Api/V1/`

**Does:**
- Authorize (`authorize()` — currently always `true` because route middleware already gates)
- Declare validation rules in `rules()`
- Are paired: `Store{X}Request` for POST, `Update{X}Request` for PATCH
- `Update` versions ignore self in unique rules using `$this->route('xxx')?->id`

**Never:**
- Touch the DB
- Decide business rules (e.g. "is_active defaults to true if not present" — that's the Action)

Example: [`app/Http/Requests/Api/V1/StoreProductRequest.php`](app/Http/Requests/Api/V1/StoreProductRequest.php)

### 10. JSON Resources — `app/Http/Resources/`

**Does:**
- Shape the API response: which fields are exposed, format dates, cast types
- Use `whenLoaded('relation', ...)` to expose relations only if eager-loaded (prevents N+1)

**Never:**
- Decide what data to load (controller/repo's job)
- Run queries

Example: [`app/Http/Resources/ProductResource.php`](app/Http/Resources/ProductResource.php)

### 11. API Controllers — `app/Http/Controllers/Api/V1/`

**Does only 4 things:**
1. Inject Repository (for read) or Action (for write) via method parameters
2. Validate via FormRequest (the request type-hint runs validation automatically)
3. Call `$repo->paginateFiltered(...)` or `$action->execute(...)`
4. Wrap the result in a `XResource` and return

**Skeleton:**

```php
public function store(StoreProductRequest $request, SaveProductAction $save): JsonResponse
{
    $product = $save->execute($request->validated());

    return (new ProductResource($product))->response()->setStatusCode(201);
}
```

That's a complete controller method. ~3 lines. Anything more = the logic is in the wrong place.

**Never:**
- Write `DB::transaction` — that belongs in the Action
- Call `Model::find($id)->update(...)` — that's repository territory
- Do anything FormRequest could do (validation)
- Do anything Resource could do (response shaping)

Example: [`app/Http/Controllers/Api/V1/ProductController.php`](app/Http/Controllers/Api/V1/ProductController.php)

### 12. Routes — `routes/`

**Does:**
- `routes/web.php` — Livewire pages via `Route::get('/foo', X\Index::class)->name('foo.index')`, gated by `middleware('auth', 'active')` (+ `role:admin` where needed)
- `routes/api.php` — REST endpoints with `auth:sanctum` + `ability:permission.name` per route group

**Never:**
- Inline closures with business logic. Closures should redirect or `view()` only.

Example: [`routes/api.php`](routes/api.php) shows the `ability:` middleware pattern.

---

## Where do I make this change? — decision table

| Change | File(s) |
|---|---|
| Add a new column | Migration + Model (`$fillable`, `$casts`) + factory |
| New validation rule | Form Object (web) **and** FormRequest (API) |
| New business rule on save (e.g. auto-archive on zero stock) | **Action only** |
| Change query/filter logic | Repository's `paginateFiltered` (or new method) |
| Add a new API field | JSON Resource only |
| Hide a UI element from a role | Blade — `@can('permission.name')` |
| New page | Livewire component + Blade view + route entry |
| New endpoint | FormRequest + Controller method + route entry |
| New scheduled job | `routes/console.php` + `app/Jobs/` |
| New external HTTP integration | New service class under `app/Services/<Domain>/` |
| Send notification on event | Event listener under `app/Listeners/` |
| New role/permission | `RoleSeeder` — append to permissions array, re-run seeder |

---

## Web vs API — same code, two surfaces

When you create a product through the web UI:

```
POST /products  ← (Livewire form submit, internal)
  → Livewire\Products\Edit::save(SaveProductAction $save)
    → ProductForm::attributes() returns validated array
    → SaveProductAction::execute(array $attrs)
      → DB::transaction
        → ProductRepository::create(array $data)
          → Product::create($data)
        → returns Product
      → returns Product
    → redirect to /products
```

When you create the same product through the API:

```
POST /api/v1/products
  → Api\V1\ProductController::store(StoreProductRequest $r, SaveProductAction $save)
    → StoreProductRequest::rules() runs (auto-validation)
    → $save->execute($r->validated())
      → DB::transaction
        → ProductRepository::create(array $data)
          → Product::create($data)
        → returns Product
      → returns Product
    → return new ProductResource($product) with 201
```

**The shared trunk** is `SaveProductAction → ProductRepository → Product`. Both presentation layers feed it the same shape of validated data. Change a business rule in `SaveProductAction` and both surfaces inherit it.

---

## SOLID — applied with file pointers

| Letter | Where in this codebase | Why |
|---|---|---|
| **S** Single Responsibility | Each layer in the diagram | One reason to change per layer |
| **O** Open/Closed | `#[Scope]` on Product; adding entities = new Repo (no `BaseRepository` edit) | Extension via subclassing/new files, no modification of working code |
| **L** Liskov Substitution | Any `RepositoryInterface` concretion works wherever the interface is expected | Test mocks substitute prod implementations seamlessly |
| **I** Interface Segregation | `RepositoryInterface` has only CRUD methods; no fat methods | New implementations don't have to stub things they don't need |
| **D** Dependency Inversion | Livewire components and API controllers depend on the repo/action via DI, not `new` | Container resolves; tests inject mocks; production injects concretes |

---

## Anti-patterns this architecture rejects

| Wrong | Why | Right |
|---|---|---|
| `Model::find($id)->update(...)` inside a controller | Couples HTTP layer to ORM; un-DRY | Call the Action |
| `$this->validate(['name'=>'required'])` inline in Livewire | Validation rules belong with the form | Use a Form Object |
| `DB::transaction` inside a Repository | Transactions express business invariants — they belong at the boundary that owns the invariant | Put it in the Action |
| `@foreach (Product::all() as $p)` in Blade | N+1 + untestable + can't cache | Pass paginated data from `render()` |
| `Action::execute()` returns an array | Loses type safety | Return a Model or a DTO |
| Empty `down()` in a migration | Breaks rollback | Always write a real inverse |
| `protected $guarded = []` | Mass-assignment exploit | Always whitelist with `$fillable` |
| `getProductsForUser()` in a controller | Controllers grow without bound | Move to repo: `findForUser()` |

---

## Adding a new feature — checklist (the 13-step pipeline)

For any non-trivial feature, in this order:

1. **Migration** — schema for new columns/tables, with indexes
2. **Model** — `$fillable`, `$casts`, relationships, log options
3. **Factory + Seeder** updates
4. **Repository** methods — `findX`, `paginateX` (`paginateFiltered` if user-facing list)
5. **Service** — only if cross-entity calculation needed (`CartCalculator`-style)
6. **Action** — `CreateXAction` / `UpdateXAction`, wraps `DB::transaction` when writing 2+ tables
7. **Policy** — record-level authorization (optional if permission-based gates suffice)
8. **Form Object** — `#[Validate]` attributes + `attributes()` method
9. **Livewire Index + Edit** — inject repos via `render()`, actions via `save()`
10. **Blade views** — Flux components, every user string wrapped in `__()`
11. **Routes** — `Route::livewire(...)->name(...)` with middleware
12. **Async work** — Job/Event/Mail if slow or non-essential
13. **Pest test** — happy path + at least one authz failure

If you skip steps, you'll usually pay later. The pipeline order isn't optional.

---

## Quick mental test before you commit

Ask yourself:

1. *"Could a junior developer find what to change for a given bug, by reading this layer's name alone?"*
2. *"If we add a mobile API tomorrow, does my new code work for it too, or did I leak logic into a controller?"*
3. *"If I rename a column, how many files break?"* — should be 1-3 (migration + model + maybe a form rule), not 10+.

If any answer is "no", the logic is in the wrong place. Move it.

---

## File map (concise)

```
app/
├── Actions/                    business operations (one per write op)
│   ├── Categories/
│   │   └── SaveCategoryAction.php
│   ├── Products/
│   │   └── SaveProductAction.php
│   └── ...
├── Contracts/
│   └── RepositoryInterface.php  CRUD contract
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/             thin REST controllers
│   │   └── Auth/               web logout
│   ├── Middleware/
│   │   └── EnsureUserActive.php
│   ├── Requests/Api/V1/        FormRequest validation per API endpoint
│   └── Resources/              JSON output shaping
├── Livewire/
│   ├── Auth/Login.php
│   ├── Dashboard/Index.php
│   ├── Forms/                  Form Objects (web validation)
│   ├── Products/{Index,Edit}.php
│   └── ...
├── Models/                     Eloquent models
├── Repositories/               query layer
│   ├── BaseRepository.php
│   ├── ProductRepository.php
│   └── ...
└── Services/                   pure calculation services
    ├── CartCalculator.php
    └── DTO/CartTotals.php

database/
├── factories/
├── migrations/
└── seeders/

resources/views/
├── components/layouts/         Flux sidebar shell + login shell
└── livewire/                   one folder per module

routes/
├── web.php                     Livewire pages
└── api.php                     /api/v1/* REST endpoints

tests/
├── Feature/Api/V1/             API CRUD tests
├── Feature/Web/                Livewire smoke tests
└── Unit/Services/              pure service tests
```

---

## When the answer feels obvious but you're not sure

Use this hierarchy of locality:

1. **Is it about validation?** → Form Object (web) or FormRequest (API)
2. **Is it about presentation (output shape, label, badge)?** → Blade or JSON Resource
3. **Is it about a database query (filtering, ordering, joining)?** → Repository
4. **Is it about an atomic group of writes (saving with stock update + audit log)?** → Action with `DB::transaction`
5. **Is it about pure math/business rules (pricing, fines)?** → Service
6. **Is it about access control?** → Route middleware first, then Livewire `mount()` or Controller's first line
7. **Is it about response shape?** → JSON Resource (API) or Blade (web)
8. **Is it about side effects (email, notification)?** → Action triggers a Job/Event; logic lives in the Job/Listener

Walk down the list — the first match wins.
