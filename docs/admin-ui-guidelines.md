# Admin UI Guidelines

Maintainer guide for the Blade + Tailwind + Alpine admin at `/quan-tri`.

## Stack

- Blade SSR layouts under `resources/views/admin/`
- Tailwind via `resources/css/admin.css` + `tailwind.admin.config.js` (Vite entry `admin`)
- Alpine 3 + `@alpinejs/focus` (focus trap for slide-overs)
- Fetch HTML partials for table swaps — not Livewire, not SPA

## UI kit — which component for what

| Need | Component / module |
|---|---|
| Index table + tabs + search + sort | `x-admin::table.*` + `resources/js/admin/table.js` |
| Slide-over create/edit / detail | `x-admin::display.slide-over` (`x-trap.noscroll`) |
| Confirm destructive action | `resources/js/admin/confirm.js` (SweetAlert2) |
| Toast / flash | Alpine store `toast` + `partials/toast-host` (`aria-live="polite"`) |
| Date / money / range / select | `form-controls.js` (flatpickr, AutoNumeric, noUiSlider, Tom Select) |
| Upload (2-stage) | `uploader.js` + `UploadStager` |
| Rich text | `rich-text.js` (Tiptap v2) — sanitize on **render**, not on save |
| Nested reorder | `sortable.js` / `menu-tree.js` (SortableJS) |
| Charts | `chart.js` (Chart.js) |

## Init / destroy contract

Every interactive widget registered in `resources/js/admin/registry.js` must implement:

```js
{ init(el), destroy(el) }
```

After any `innerHTML` / partial swap:

1. `AdminRegistry.destroyTree(oldRoot)`
2. Replace DOM
3. `AdminRegistry.initTree(newRoot)` + `Alpine.initTree(newRoot)` when Alpine nodes were injected

Do not attach library instances without a matching destroy path — leaks break the next swap.

## Transactions

- Module write routes (POST/PUT/PATCH/DELETE) run under `admin.transaction` (`AdminDatabaseTransaction`).
- Auth routes and `routes/admin/ui.php` (upload / options / ui-kit) intentionally omit the transaction middleware so file I/O does not hold a DB lock.
- Multi-step writes (bus + service sync, menu tree reorder, default web profile switch) must stay in one request so the middleware can roll them back together.

## Upload (two stages)

1. `POST admin.api.upload.process` → stage under non-public tmp, return opaque session token (`sessionId~…`).
2. On successful form save → `UploadStager::commit()` moves into the public disk directory for that module.
3. `DELETE admin.api.upload.revert` accepts **only** tokens from the current session — raw paths and cross-session tokens are rejected.
4. Failed form validation must not leave committed public files; only tmp staging may exist until the prune job cleans it.

## Security habits

- Never `$model->fill($request->all())`. Use FormRequests + explicit field lists (`fillableData()` / validated arrays).
- Booking `status`, `payment_status`, `confirmed_at`, `payment_transaction_id` are not fillable — change only via action endpoints.
- Sort/filter keys must go through `TableConfig` whitelists.
- Public HTML sinks (`{!! !!}`) must run through `HtmlSanitizer::sanitize()` / `sanitizeMap()` (see client page show).
- Sidebar SVG icons are hardcoded in `Navigation` — do not interpolate user input into `{!! $item['icon'] !!}`.

## Accessibility baseline

- Icon-only buttons need `aria-label`.
- Slide-overs: `role="dialog"`, `aria-modal`, focus trap (`x-trap`), restore focus to the opener on close.
- Toasts use `aria-live="polite"`.
- Desktop-only (≥1280px) — no mobile layout requirement.

## Adding a module

1. Controller under `app/Http/Controllers/Admin/`
2. FormRequests under `app/Http/Requests/Admin/`
3. Routes in a dedicated `routes/admin/*.php` file (picked up by `bootstrap/app.php`)
4. Views under `resources/views/admin/{module}/`
5. Register nav item in `App\Support\Admin\Navigation` if it is a top-level menu entry
6. Feature tests under `tests/Feature/Admin/`
