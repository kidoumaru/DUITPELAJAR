# Implementation Plan: Admin Dashboard

## Overview

This plan implements the Admin Panel for DuitPelajar (Laravel 13 / PHP 8.3 / Tailwind v4 / Chart.js / SQLite) by adding role-based access control, a dedicated admin layout and views, centralizing category management, and adding announcements, a message/help center, educational articles, statistics, and admin profile management — all isolated under `/admin` behind triple middleware, with no changes to existing user-facing behavior except making `categories/index.blade.php` read-only.

Implementation is staged so each layer validates before the next is built: database schema and enums first, then models, then authorization, then controllers, then a single consolidated routes wiring, then admin/user-facing views, then the full property and regression test suite. All code follows existing conventions (Eloquent with `#[Fillable]`/scopes, named routes, Indonesian validation messages, `@push('scripts')` for JS, anonymous-class migrations).

Language: PHP 8.3 (already used throughout the codebase). Tests use PHPUnit via `php artisan test`.

## Tasks

- [x] 1. Database migrations (schema foundation)
  - [x] 1.1 Create migration adding `role` enum (`admin`/`user`, default `user`), `is_active` boolean (default true), and nullable `photo` to the `users` table after `school_level`
    - Reversible `down()` drops the three columns
    - _Requirements: 11.1, 11.2_
  - [x] 1.2 Create migration adding `is_active` boolean (default true) to `categories` after `is_system`, then centralize by setting all `user_id` values to `null`
    - `down()` drops the `is_active` column
    - _Requirements: 4.13, 4.14, 11.3_
  - [x] 1.3 Create `announcements` table migration with `id`, `title`, `longText body`, `status` enum (`published`/`draft`, default `draft`), nullable `published_at`, `author_id` FK to `users` (cascade delete), timestamps, and an index on `(status, published_at)`
    - _Requirements: 11.4, 11.8, 11.9_
  - [x] 1.4 Create `messages` table migration (conversations) with `id`, `user_id` FK (cascade delete), `title`, `longText body`, `status` enum (`new`/`processing`/`done`, default `new`), timestamps, and indexes on `(user_id, status)` and `status`
    - _Requirements: 11.5, 11.8, 11.9_
  - [x] 1.5 Create `message_replies` table migration with `id`, `message_id` FK (cascade delete), `sender_id` FK (cascade delete), `longText body`, timestamps, and an index on `(message_id, created_at)`
    - _Requirements: 11.6, 11.8, 11.9_
  - [x] 1.6 Create `articles` table migration with `id`, `title`, unique `slug`, nullable `thumbnail`, `longText content`, `category` string, `status` enum (`published`/`draft`, default `draft`), nullable `published_at`, `author_id` FK (cascade delete), timestamps, and indexes on `(status, published_at)` and `slug`
    - _Requirements: 11.7, 11.8, 11.9_
  - [ ]* 1.7 Write `tests/Feature/Admin/MigrationTest.php` covering schema correctness and reversibility
    - Schema smoke tests: assert each new table/column exists with correct type, default, foreign key, and index after `up()`
    - **Property 35: Migration reversibility** — running `up()` then `down()` restores the schema to its prior state (added tables removed, added columns dropped)
    - **Property 18: Migration converts user categories to global** — any category with non-null `user_id` has `user_id` set to `null` while preserving the record and its ID
    - **Validates: Requirements 11.1–11.9, 4.14**

- [x] 2. Enums and models (domain layer)
  - [x] 2.1 Create `App\Models\Role` backed string enum (`Admin = 'admin'`, `User = 'user'`)
    - _Requirements: 1.1, Glossary (Role)_
  - [x] 2.2 Create `App\Models\AnnouncementStatus` backed string enum (`Published`, `Draft`)
    - _Requirements: 5.4–5.6, 11.4_
  - [x] 2.3 Create `App\Models\ArticleStatus` backed string enum (`Published`, `Draft`)
    - _Requirements: 7.4–7.5, 11.7_
  - [x] 2.4 Create `App\Models\MessageStatus` backed string enum (`New`, `Processing`, `Done`)
    - _Requirements: 6.9, 11.5_
  - [x] 2.5 Modify `App\Models\User`: add `is_active` and `photo` to the `#[Fillable]` whitelist (keep `role` OUT of fillable to prevent privilege escalation), add casts for `role` (Role enum) and `is_active` (boolean), add `isAdmin()` helper, and add relationships `announcements()`, `articles()`, `messages()` (author_id / user_id); keep existing relationships and `customCategories()`
    - _Requirements: 12.2, 9.1, Glossary (Admin/RegularUser)_
  - [x] 2.6 Modify `App\Models\Category`: add `is_active` to `$fillable` and casts (boolean), add `scopeActive` (where `is_active` true), add `isDeletable()` (returns true iff zero transactions), and remove the now-obsolete `scopeAvailableFor` and `isDeletableBy` (all categories are global after centralization); keep `scopeIncome`, `scopeExpense`, relationships
    - _Requirements: 4.6, 4.7, 4.9, 4.14, 12.2_
  - [x] 2.7 Create `App\Models\Announcement` with `#[Fillable](['title','body','status','author_id','published_at'])`, casts for `status` (AnnouncementStatus) and `published_at` (datetime), `author()` relation, and `scopeVisibleToUsers` (published AND published_at null or <= now)
    - _Requirements: 5.4, 5.5, 5.6, 12.2_
  - [x] 2.8 Create `App\Models\Message` with `#[Fillable](['user_id','title','body','status'])`, cast for `status` (MessageStatus), `user()` and `replies()` relations (replies ordered by `created_at` ascending)
    - _Requirements: 6.1, 6.3, 6.9, 12.2_
  - [x] 2.9 Create `App\Models\MessageReply` with `#[Fillable](['message_id','sender_id','body'])`, `message()` and `sender()` relations
    - _Requirements: 6.5, 6.6, 12.2_
  - [x] 2.10 Create `App\Models\Article` with `#[Fillable](['title','slug','thumbnail','content','category','status','author_id','published_at'])`, casts for `status` (ArticleStatus) and `published_at` (datetime), `author()` relation, and `scopeVisibleToUsers`
    - _Requirements: 7.1, 7.4, 7.5, 12.2_
  - [ ]* 2.11 Start `tests/Feature/Admin/SecurityTest.php` with the mass-assignment property
    - **Property 33: Fillable mass-assignment protection on every model** — every model class (existing and new) defines an explicit `$fillable` or `#[Fillable]`, and `role` is not fillable on User
    - **Validates: Requirements 12.2**

- [ ] 3. Authorization infrastructure (access control)
  - [x] 3.1 Create `App\Http\Middleware\EnsureUserIsAdmin` that aborts with HTTP 403 ("Anda tidak memiliki akses ke halaman admin.") when the user is missing or `isAdmin()` is false
    - Applied inline in the admin route group (not aliased in bootstrap/app.php)
    - _Requirements: 1.2, 1.3_
  - [x] 3.2 Add the `admin:promote {email}` Artisan command to `routes/console.php`: look up the user by email, set `role = Role::Admin` and save, print a confirmation; for an unknown email print an error and change nothing
    - _Requirements: 1.4, 1.5_
  - [x] 3.3 Modify `AuthController::login`: after successful `Auth::attempt`, reject deactivated accounts (`is_active === false`) by logging out, invalidating the session, and returning an error; otherwise regenerate the session, persist `active_session_id`, clear old sessions (existing single-session logic), and redirect to `admin.dashboard` for admins or `dashboard` for regular users
    - _Requirements: 1.6, 1.7, 1.8, 3.7_
  - [ ]* 3.4 Write `tests/Feature/Admin/AuthorizationTest.php` for login/promote behavior
    - **Property 1: Login redirect determined by role** — admin → admin dashboard, user → user dashboard
    - **Property 3: admin:promote promotes existing users** — sets role to admin and prints confirmation
    - **Property 4: admin:promote rejects unknown emails** — prints error, no role changes
    - **Property 10: Deactivated accounts cannot log in** — rejected with error, no session established
    - Unit examples: admin login redirect, regular-user login redirect, unauthenticated redirect to login, single-session enforcement still applies to admins
    - **Validates: Requirements 1.4–1.8, 3.7, 13.1, 13.2, 13.3**

- [x] 4. User-facing category centralization (controllers + read-only view)
  - [x] 4.1 Modify `TransactionController`: in `index`, `create`, and `edit` use `Category::active()->orderBy('name')->get()` instead of `Category::availableFor($user->id)`; in `store` and `update` change the `category_id` rule to `Rule::exists('categories','id')->where(fn($q) => $q->where('is_active', true))`
    - _Requirements: 4.9, 4.10, 4.11_
  - [x] 4.2 Reduce `CategoryController` to a read-only `index` that returns `Category::active()->income()` and `Category::active()->expense()` ordered by name; remove `store`, `update`, and `destroy`
    - _Requirements: 4.8_
  - [x] 4.3 Modify `resources/views/categories/index.blade.php` to read-only: remove the "Kategori Baru" button, the add/edit modals, and the edit/delete action buttons (including the `editCategory` script), keeping the income/expense listing display
    - _Requirements: 4.8, 10.6, 12.6_
  - [ ]* 4.4 Start `tests/Feature/Admin/CategoryManagementTest.php` with the transaction-side properties
    - **Property 16: Transaction forms show only active categories and enforce active selection** — create/edit forms list only active categories; a transaction referencing a deactivated category is rejected with a validation error; one referencing an active category is accepted and persisted
    - **Property 17: Converted category references remain intact** — a transaction whose category was converted from a user-created category to a global category still resolves `category_id` and displays correctly
    - **Validates: Requirements 4.9, 4.10, 4.11, 4.12, 13.6, 13.7, 13.8**

- [ ] 5. Admin and user-facing controllers (new content)
  - [x] 5.1 Create `AdminDashboardController::index` returning aggregate counts (total users, active users, total transactions, income categories, expense categories, incoming `new` messages, published announcements) and a 12-month user-growth dataset for Chart.js; never query individual transaction amounts
    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  - [x] 5.2 Create `AdminStatisticController::index` returning total transactions, help message count, published articles, published announcements, a 12-month user-growth dataset, and users grouped by registration period (last year); no individual financial data
    - _Requirements: 8.1, 8.2_
  - [x] 5.3 Create `AdminUserController` with `index` (search by name/email, paginate 15), `show` (profile details, no financial data), `activate`, `deactivate`, and `destroy` (block deletion of admin accounts)
    - _Requirements: 3.1–3.10_
  - [x] 5.4 Create `AdminCategoryController` with `index` (filter by type, `withCount('transactions')`, paginate 15), `store`, `update`, `activate`, `deactivate`, and `destroy` (blocked when transactions exist); backend validation with Indonesian messages
    - _Requirements: 4.1–4.7_
  - [x] 5.5 Create `AdminAnnouncementController` with `index` (paginate 15), `store`, `update`, `destroy`; validate title/body/status/published_at; set `author_id` from the authenticated admin
    - _Requirements: 5.1, 5.2, 5.3_
  - [x] 5.6 Create `AdminMessageController` with `index` (filter by status, paginate 15), `show`, `reply` (stores reply with admin as sender, sets status to `processing`), and `markDone` (sets status to `done`)
    - _Requirements: 6.4, 6.5, 6.7_
  - [x] 5.7 Create `AdminArticleController` with `index` (paginate 15), `store`, `update`, `destroy`; validate fields; handle thumbnail upload (store on `public` disk); generate a unique slug via `ensureUniqueSlug`
    - _Requirements: 7.1, 7.2, 7.3, 7.6_
  - [x] 5.8 Create `AdminProfileController` with `show`, `update` (name/email, unique email), `updatePassword` (current_password + confirmed min-8, hashed via `Hash::make`), and `updatePhoto` (image upload, store on `public` disk); Indonesian validation messages
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_
  - [x] 5.9 Create user-facing `MessageController` with `index` (own conversations only), `create`, `store` (creates conversation owned by the user with status `new`), `show` and `reply` (both `abort(403)` if `user_id !== Auth::id()`)
    - _Requirements: 6.1, 6.2, 6.3, 6.6, 6.8, 12.7_
  - [x] 5.10 Create user-facing `AnnouncementController` with `index` (`visibleToUsers`, paginate 10) and `show` (`abort_unless` published-and-due, else 404)
    - _Requirements: 5.4, 5.5, 5.6_
  - [x] 5.11 Create user-facing `ArticleController` with `index` (`visibleToUsers`, paginate 10) and `show` (`abort_unless` published-and-due, else 404)
    - _Requirements: 7.4, 7.5_

- [x] 6. Routes (consolidated wiring)
  - [x] 6.1 Edit `routes/web.php` once to: add the user-facing message/announcement/article routes inside the existing authenticated group; remove the user `categories.store`/`categories.update`/`categories.destroy` routes (keep `categories.index`); add the `Route::middleware(['auth', EnsureSingleLoginSession::class, EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')` group with dashboard, users, categories, announcements, messages, articles, statistics, and profile routes (named `admin.*`); add the controller `use` imports
    - _Requirements: 1.1, 1.3, 4.8, 6.2, 10.2_

- [ ] 7. Admin layout and views
  - [x] 7.1 Create `resources/views/layouts/admin.blade.php` separate from `app.blade.php`: Tailwind v4, Plus Jakarta Sans, indigo/blue gradient, Indonesian text, responsive sidebar with the nine items in order (Dashboard, Manajemen Pengguna, Manajemen Kategori, Pengumuman, Pesan dan Bantuan, Artikel Edukasi, Statistik Aplikasi, Profil Admin, Logout as POST form), `csrf-token` meta, flash-message block, `@yield('content')`, and `@stack('scripts')`
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 12.1_
  - [-] 7.2 Create `resources/views/admin/dashboard/index.blade.php`: stat cards for the seven aggregates plus a Chart.js user-growth chart pushed via `@push('scripts')`; no individual financial data
    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  - [-] 7.3 Create `resources/views/admin/statistics/index.blade.php`: aggregate stat cards, user-growth chart, and users-by-period table; no individual financial data
    - _Requirements: 8.1, 8.2_
  - [-] 7.4 Create `resources/views/admin/users/index.blade.php` (searchable, paginated table of 15 with activate/deactivate/delete actions, all forms `@csrf` + `@method`) and `show.blade.php` (profile details: name, email, school_level, role, status; no transaction amounts/balances)
    - _Requirements: 3.1–3.6, 3.8, 3.9, 10.3_
  - [-] 7.5 Create `resources/views/admin/categories/index.blade.php`: table with create/edit modals (name, type, icon, color, is_active) and activate/deactivate/delete actions; `@csrf` on all forms
    - _Requirements: 4.1–4.7, 10.3_
  - [-] 7.6 Create `resources/views/admin/announcements/index.blade.php`: table with create/edit modals (title, body, status, published_at) and delete action; `@csrf` on all forms
    - _Requirements: 5.1–5.3, 10.3_
  - [~] 7.7 Create `resources/views/admin/messages/index.blade.php` (all conversations with status filter) and `show.blade.php` (thread with replies in chronological order, admin reply form, mark-done action); `@csrf` on all forms
    - _Requirements: 6.3, 6.4, 6.5, 6.7, 10.3_
  - [~] 7.8 Create `resources/views/admin/articles/index.blade.php`: table with create/edit modals (title, content, category, status, published_at, thumbnail upload) and delete action; `@csrf` on all forms, `enctype="multipart/form-data"` for uploads
    - _Requirements: 7.1–7.3, 7.6, 10.3_
  - [~] 7.9 Create `resources/views/admin/profile/show.blade.php`: profile form (name, email), password change form, and photo upload form; `@csrf` on all forms
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 10.3_

- [ ] 8. User-facing views (new content)
  - [~] 8.1 Create `resources/views/messages/index.blade.php` (user's own conversations), `create.blade.php` (new conversation form), and `show.blade.php` (thread with reply form), all `@csrf`-protected and extending `layouts.app`
    - _Requirements: 6.1, 6.2, 6.3, 6.6, 12.1_
  - [~] 8.2 Create `resources/views/announcements/index.blade.php` (published list) and `show.blade.php` (single announcement), extending `layouts.app`
    - _Requirements: 5.4, 5.5, 5.6_
  - [~] 8.3 Create `resources/views/articles/index.blade.php` (published list) and `show.blade.php` (single article with thumbnail), extending `layouts.app`
    - _Requirements: 7.4, 7.5, 7.6_

- [ ] 9. Testing and final verification (full-stack property tests)
  - [ ]* 9.1 Append to `tests/Feature/Admin/AuthorizationTest.php`
    - **Property 2: Admin middleware blocks non-admins with 403** — any authenticated non-admin hitting any `/admin` route gets HTTP 403; unauthenticated visitors are redirected to login
    - **Validates: Requirements 1.2, 1.3, 4.1, 13.3, 13.4**
  - [ ]* 9.2 Write `tests/Feature/Admin/DashboardTest.php`
    - **Property 5: Aggregate statistics match database state** — dashboard counts exactly match database aggregates
    - **Property 6: No individual financial data exposed** — the response contains no individual user transaction amounts or balances
    - **Validates: Requirements 2.1, 2.4, 8.1, 8.2, 13.14**
  - [ ]* 9.3 Write `tests/Feature/Admin/UserManagementTest.php`
    - **Property 7: User list paginated at 15 per page** — each non-final page has exactly 15 records
    - **Property 8: User search filters by name and email** — every returned user's name or email contains the query fragment
    - **Property 9: User activation toggles is_active**
    - **Property 11: User deletion outcome determined by role** — admin deletion prevented; user deletion removes the record
    - **Property 12: Deactivation preserves account data** — all fields except `is_active` unchanged
    - **Validates: Requirements 3.1–3.10, 13.5, 13.13**
  - [ ]* 9.4 Append to `tests/Feature/Admin/CategoryManagementTest.php`
    - **Property 13: Category create and update persist all fields** (name, type, icon, color, is_active)
    - **Property 14: Category is_active toggle** — activate sets true, deactivate sets false
    - **Property 15: Category deletion gated by transaction count** — succeeds iff zero transactions
    - **Validates: Requirements 4.1–4.7, 13.5**
  - [ ]* 9.5 Write `tests/Feature/Admin/AnnouncementTest.php`
    - **Property 19: Announcement CRUD persistence** — create persists title/body/status/author_id/published_at; update persists changes; delete removes the record
    - **Property 20: Announcement visibility by status and date** — visible to users iff status published and published_at null or past/present; draft or future-published hidden
    - **Validates: Requirements 5.1–5.6, 13.9**
  - [ ]* 9.6 Write `tests/Feature/Admin/MessageCenterTest.php`
    - **Property 21: Conversation creation sets status to new**
    - **Property 22: Conversation ownership privacy** — user sees only own conversations; accessing another user's conversation returns 403
    - **Property 23: Admin sees all conversations**
    - **Property 24: Admin reply sets conversation status to processing**
    - **Property 25: Conversation replies in chronological order** (created_at ascending)
    - **Property 26: Conversation status done is persisted**
    - **Validates: Requirements 6.1–6.9, 12.7, 13.10, 13.11**
  - [ ]* 9.7 Write `tests/Feature/Admin/ArticleTest.php`
    - **Property 27: Article CRUD persistence** — create persists title/slug/thumbnail/content/category/status/author_id/published_at; update persists changes; delete removes the record
    - **Property 28: Article visibility by status and date** — visible iff published and published_at null or past/present; draft hidden
    - **Property 29: File upload stores and records path** — uploaded thumbnail/profile photo is stored and its path recorded
    - **Validates: Requirements 7.1–7.6, 13.12**
  - [ ]* 9.8 Write `tests/Feature/Admin/AdminProfileTest.php`
    - **Property 30: Admin profile updates persist changes** (name/email validated and persisted)
    - **Property 31: Password hashing on update** — stored value verifies against plaintext and does not equal plaintext
    - **Validates: Requirements 9.2, 9.3, 12.4**
  - [ ]* 9.9 Append to `tests/Feature/Admin/SecurityTest.php`
    - **Property 32: CSRF tokens on all admin forms** — every rendered admin form includes a CSRF token field
    - **Property 34: Backend validation before persist** — invalid AdminPanel submissions are rejected with validation errors and no database change
    - **Validates: Requirements 12.1, 12.3, 13.14**
  - [ ]* 9.10 Write `tests/Feature/Admin/BackwardCompatTest.php`
    - **Property 36: Existing user views unchanged** — every existing user-facing view file other than `categories/index.blade.php` is unchanged after implementation; existing user routes (dashboard, transactions, budgets, savings-goals, reports, profile, logout) remain functional
    - **Validates: Requirements 10.6, 12.5, 12.6, 13.14**
  - [~] 9.11 Final checkpoint — run the full suite
    - Run `php artisan test` (or `php artisan test --filter=Admin`) and ensure zero errors and zero failing assertions; ask the user if questions arise.
    - _Requirements: 13.14_

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP, but the security, privacy, and regression properties they encode are recommended before shipping.
- Each task references specific requirement clauses (e.g., 4.9) for traceability back to `requirements.md`; properties reference their number from the design's "Correctness Properties" section.
- Migrations use anonymous-class style with reversible `down()` methods; enum and model placement follows the existing `app/Models/` convention.
- `role` is intentionally excluded from the User `$fillable` whitelist — only `admin:promote` sets it via direct assignment, preventing privilege escalation.
- The admin route group applies middleware inline (`['auth', EnsureSingleLoginSession::class, EnsureUserIsAdmin::class]`) to match the existing inline-middleware pattern.
- Only one existing user view changes: `categories/index.blade.php` becomes read-only. All other existing user views and routes are untouched.
- Property tests are organized into one test file per feature area (matching the design's test structure); self-contained tests (migrations, fillable, login/promote, transaction categories) are written early near their implementation, while full-stack feature tests run after routes and views are wired.
## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3", "1.4", "1.5", "1.6", "2.1", "2.2", "2.3", "2.4"] },
    { "id": 1, "tasks": ["1.7", "2.5", "2.6", "2.7", "2.8", "2.9", "2.10", "3.1", "7.1"] },
    { "id": 2, "tasks": ["2.11", "3.2", "3.3", "4.1", "4.2", "5.1", "5.2", "5.3", "5.4", "5.5", "5.6", "5.7", "5.8", "5.9", "5.10", "5.11"] },
    { "id": 3, "tasks": ["3.4", "4.3", "4.4", "6.1"] },
    { "id": 4, "tasks": ["7.2", "7.3", "7.4", "7.5", "7.6", "7.7", "7.8", "7.9", "8.1", "8.2", "8.3"] },
    { "id": 5, "tasks": ["9.1", "9.2", "9.3", "9.4", "9.5", "9.6", "9.7", "9.8", "9.9", "9.10"] },
    { "id": 6, "tasks": ["9.11"] }
  ]
}
```
