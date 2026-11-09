# Design Document: Admin Dashboard

## Introduction

This design document translates the admin-dashboard requirements into a concrete technical architecture for the DuitPelajar Laravel 13 / PHP 8.3 application. It covers database schema, models, middleware, controllers, routes, views, an Artisan command, security, backward compatibility, and formal correctness properties.

All code examples use PHP 8.3 and follow the conventions already established in the codebase: Eloquent with `$fillable`/scopes, named routes, Indonesian validation messages, `@push('scripts')` for JS, and the `#[Fillable]` / `#[Hidden]` PHP 8.3 attributes on the User model.

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      routes/web.php                          │
│                                                              │
│  Existing user routes (auth + EnsureSingleLoginSession)      │
│  ├── dashboard, transactions, budgets, savings-goals        │
│  ├── categories (READ-ONLY after centralization)             │
│  ├── reports, profile                                       │
│  ├── messages (user-facing help center)            [NEW]     │
│  ├── announcements (user-facing, published only)  [NEW]      │
│  └── articles (user-facing, published only)      [NEW]      │
│                                                              │
│  Admin routes (auth + EnsureSingleLoginSession                │
│               + EnsureUserIsAdmin) /admin prefix              │
│  ├── dashboard           (AdminDashboardController)          │
│  ├── users                (AdminUserController)              │
│  ├── categories           (AdminCategoryController)           │
│  ├── announcements        (AdminAnnouncementController)       │
│  ├── messages             (AdminMessageController)            │
│  ├── articles             (AdminArticleController)            │
│  ├── statistics           (AdminStatisticController)          │
│  └── profile              (AdminProfileController)            │
└─────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│                    SQLite Database                           │
│  users ( + role, is_active, photo )                          │
│  categories ( + is_active, user_id → null )                   │
│  transactions (unchanged)                                    │
│  announcements            [NEW]                              │
│  messages                 [NEW]                              │
│  message_replies          [NEW]                              │
│  articles                 [NEW]                              │
└─────────────────────────────────────────────────────────────┘
```

The admin panel is fully isolated: a dedicated `EnsureUserIsAdmin` middleware guards every `/admin/*` route, a separate Blade layout (`admin.blade.php`) renders admin pages, and no existing user-facing view file is modified except `categories/index.blade.php` (which becomes read-only per Requirement 4.8).

---

## Database Schema and Migrations

Six new migration files are added to `database/migrations/`. All follow the existing anonymous-class style with `up()` and reversible `down()` methods.

### Migration 1: Add role, is_active, and photo to users table

```php
<?php
// add_role_is_active_photo_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('school_level');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('photo')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'photo']);
        });
    }
};
```

### Migration 2: Add is_active to categories and centralize

```php
<?php
// add_is_active_to_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_system');
        });

        // Convert all user-created categories to global (Requirement 4.14).
        DB::table('categories')->whereNotNull('user_id')->update(['user_id' => null]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
```

### Migration 3: Create announcements table

```php
<?php
// create_announcements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('body');
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
```

### Migration 4: Create messages table (conversations)

```php
<?php
// create_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->enum('status', ['new', 'processing', 'done'])->default('new');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
```

### Migration 5: Create message_replies table

```php
<?php
// create_message_replies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->longText('body');
            $table->timestamps();

            $table->index(['message_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_replies');
    }
};
```

### Migration 6: Create articles table

```php
<?php
// create_articles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->longText('content');
            $table->string('category');
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
```

---

## Enums

PHP 8.3 backed string enums are placed in `app/Models/` (no new base directories) following the CLAUDE.md convention of TitleCase case names.

```php
<?php
// app/Models/Role.php
namespace App\Models;

enum Role: string
{
    case Admin = 'admin';
    case User = 'user';
}
```

```php
<?php
// app/Models/AnnouncementStatus.php
namespace App\Models;

enum AnnouncementStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
```

```php
<?php
// app/Models/ArticleStatus.php
namespace App\Models;

enum ArticleStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
```

```php
<?php
// app/Models/MessageStatus.php
namespace App\Models;

enum MessageStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Done = 'done';
}
```

---

## Models

### Modified: User

Add `role`, `is_active`, and `photo` to the fillable attribute and casts. Note that `role` is intentionally **excluded** from `$fillable` to prevent privilege escalation via mass assignment; the `admin:promote` command sets it via direct assignment.

```php
<?php
// app/Models/User.php  (modified portions only)

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['name', 'email', 'password', 'school_level', 'is_active', 'photo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    // ...

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    // Convenience methods
    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    // Existing relationships unchanged
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function budgets() { return $this->hasMany(Budget::class); }
    public function savingsGoals() { return $this->hasMany(SavingsGoal::class); }

    // Kept for backward compatibility; returns empty after centralization
    public function customCategories() { return $this->hasMany(Category::class); }

    // New relationships
    public function announcements() { return $this->hasMany(Announcement::class, 'author_id'); }
    public function articles() { return $this->hasMany(Article::class, 'author_id'); }
    public function messages() { return $this->hasMany(Message::class); }
}
```

### Modified: Category

Add `is_active` to fillable and casts. Replace `scopeAvailableFor` with an active-only scope since all categories are now global (`user_id` is null after migration). The `isDeletableBy` method is replaced by an admin-centric `isDeletable()` that checks transaction count regardless of ownership.

```php
<?php
// app/Models/Category.php  (modified portions only)

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function budgets() { return $this->hasMany(Budget::class); }

    // Replaces scopeAvailableFor: after centralization all categories are
    // global, so "available" = active.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome($query) { return $query->where('type', 'income'); }
    public function scopeExpense($query) { return $query->where('type', 'expense'); }

    // Admin deletion check: a category is deletable iff it has zero transactions.
    public function isDeletable(): bool
    {
        return $this->transactions()->count() === 0;
    }
}
```

### New: Announcement

```php
<?php
// app/Models/Announcement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'body', 'status', 'author_id', 'published_at'])]
class Announcement extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AnnouncementStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Visible to regular users: published AND (no date OR date <= now)
    public function scopeVisibleToUsers($query)
    {
        return $query->where('status', AnnouncementStatus::Published)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
```

### New: Message (Conversation)

```php
<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'title', 'body', 'status'])]
class Message extends Model
{
    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(MessageReply::class)->orderBy('created_at');
    }
}
```

### New: MessageReply

```php
<?php
// app/Models/MessageReply.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['message_id', 'sender_id', 'body'])]
class MessageReply extends Model
{
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
```

### New: Article

```php
<?php
// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'slug', 'thumbnail', 'content', 'category', 'status', 'author_id', 'published_at'])]
class Article extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeVisibleToUsers($query)
    {
        return $query->where('status', ArticleStatus::Published)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
```

---

## Middleware

### EnsureUserIsAdmin

A new middleware that returns HTTP 403 for any non-admin user. It is applied inline in the admin route group (matching the existing pattern of inline middleware in `routes/web.php`, not aliased in `bootstrap/app.php`).

```php
<?php
// app/Http/Middleware/EnsureUserIsAdmin.php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
```

The middleware chain for admin routes is `['auth', EnsureSingleLoginSession::class, EnsureUserIsAdmin::class]`, applied in sequence. The `auth` middleware handles unauthenticated visitors (redirect to login), `EnsureSingleLoginSession` enforces single-session, and `EnsureUserIsAdmin` enforces the role check.

---

## Controllers

All admin controllers extend the abstract `App\Http\Controllers\Controller`. Each returns `View` for GET routes and `RedirectResponse` for POST/PUT/DELETE, matching existing conventions.

### AdminDashboardController

Displays aggregate statistics and a user-growth chart. Queries only counts and aggregates — never individual transaction amounts.

```php
<?php
// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Category;
use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalTransactions = Transaction::count();
        $incomeCategories = Category::income()->count();
        $expenseCategories = Category::expense()->count();
        $incomingMessages = Message::where('status', MessageStatus::New)->count();
        $publishedAnnouncements = Announcement::where('status', 'published')->count();

        // User growth chart data: new users per month for last 12 months
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $userGrowth[] = [
                'label' => $date->locale('id')->translatedFormat('M Y'),
                'count' => $count,
            ];
        }

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'activeUsers',
            'totalTransactions',
            'incomeCategories',
            'expenseCategories',
            'incomingMessages',
            'publishedAnnouncements',
            'userGrowth',
        ));
    }
}
```

### AdminUserController

Handles listing, searching, viewing, activating, deactivating, and deleting users. Deletion is blocked for admin accounts (self-protection). Viewing a user profile exposes no financial data.

```php
<?php
// app/Http/Controllers/AdminUserController.php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        // Deliberately do NOT load transactions/amounts — no financial data.
        return view('admin.users.show', compact('user'));
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$user->name}\" berhasil diaktifkan.");
    }

    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$user->name}\" berhasil dinonaktifkan.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === Role::Admin) {
            return back()->with('error', 'Akun admin tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$name}\" berhasil dihapus.");
    }
}
```

### AdminCategoryController

Full CRUD plus activate/deactivate. Deletion is gated by transaction count (Requirement 4.6/4.7). All categories are global (`user_id` is null).

```php
<?php
// app/Http/Controllers/AdminCategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query();

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        $categories = $query->withCount('transactions')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori harus Pemasukan atau Pengeluaran.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? 'tag',
            'color' => $validated['color'] ?? '#3b82f6',
            'is_system' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    public function activate(Category $category): RedirectResponse
    {
        $category->update(['is_active' => true]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diaktifkan.");
    }

    public function deactivate(Category $category): RedirectResponse
    {
        $category->update(['is_active' => false]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil dinonaktifkan.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan pada transaksi."
            );
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
```

### AdminAnnouncementController

CRUD with status (`published`/`draft`) and `published_at`. Uses the `scopeVisibleToUsers` scope for user-facing views.

```php
<?php
// app/Http/Controllers/AdminAnnouncementController.php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with('author')->latest()->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'body.required' => 'Isi pengumuman wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        Announcement::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => AnnouncementStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
            'author_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => AnnouncementStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
```

### AdminMessageController

Admin sees all conversations, can reply (sets status to `processing`), and can mark a conversation `done`.

```php
<?php
// app/Http/Controllers/AdminMessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Message::with(['user', 'replies']);

        if ($request->filled('status') && in_array($request->status, ['new', 'processing', 'done'])) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(Message $message): View
    {
        $message->load(['user', 'replies.sender']);

        return view('admin.messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ], [
            'body.required' => 'Balasan wajib diisi.',
        ]);

        MessageReply::create([
            'message_id' => $message->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        $message->update(['status' => MessageStatus::Processing]);

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Balasan terkirim.');
    }

    public function markDone(Message $message): RedirectResponse
    {
        $message->update(['status' => MessageStatus::Done]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Percakapan ditandai selesai.');
    }
}
```

### AdminArticleController

CRUD with thumbnail upload, auto-generated unique slug, and status-based visibility.

```php
<?php
// app/Http/Controllers/AdminArticleController.php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('author')->latest()->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'content.required' => 'Konten artikel wajib diisi.',
            'category.required' => 'Kategori artikel wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')
                ->store('article-thumbnails', 'public');
        }

        $baseSlug = Str::slug($validated['title']);
        $slug = $this->ensureUniqueSlug($baseSlug);

        Article::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'category' => $validated['category'],
            'status' => ArticleStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
            'thumbnail' => $thumbnailPath,
            'author_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('article-thumbnails', 'public');
        }

        $validated['status'] = ArticleStatus::from($validated['status']);

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    private function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
```

### AdminStatisticController

Displays detailed aggregate statistics. Shares the aggregation logic with the dashboard but provides more detail (users by period, article/announcement/message counts).

```php
<?php
// app/Http/Controllers/AdminStatisticController.php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\Message;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminStatisticController extends Controller
{
    public function index(): View
    {
        $totalTransactions = Transaction::count();
        $helpMessages = Message::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $publishedAnnouncements = Announcement::where('status', 'published')->count();

        // User growth chart (12 months)
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userGrowth[] = [
                'label' => $date->locale('id')->translatedFormat('M Y'),
                'count' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        }

        // Users grouped by registration period (by month)
        $usersByPeriod = User::selectRaw(
            "strftime('%Y-%m', created_at) as period, COUNT(*) as total"
        )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.statistics.index', compact(
            'totalTransactions',
            'helpMessages',
            'publishedArticles',
            'publishedAnnouncements',
            'userGrowth',
            'usersByPeriod',
        ));
    }
}
```

### AdminProfileController

Manages the admin's own profile: name/email update, password change (hashed), and photo upload. Reuses the existing hashing mechanism (`Hash::make` and the `hashed` cast).

```php
<?php
// app/Http/Controllers/AdminProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function show(): View
    {
        $admin = request()->user();

        return view('admin.profile.show', compact('admin'));
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ], [
            'photo.required' => 'Foto wajib diunggah.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = $request->file('photo')->store('profile-photos', 'public');

        $admin->update(['photo' => $path]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }
}
```

### Modified: AuthController

Two changes to the existing `login` method:
1. **Role-based redirect** — admin goes to `admin.dashboard`, regular user goes to `dashboard` (Requirements 1.6, 1.7).
2. **Deactivated account rejection** — if `is_active` is false, log out and return an error (Requirement 3.7).

```php
<?php
// app/Http/Controllers/AuthController.php — modified login() method

public function login(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ], [
        'email.required' => 'Alamat email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ]);

    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $user = Auth::user();

        // Reject deactivated accounts (Requirement 3.7).
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user->forceFill([
            'active_session_id' => $request->session()->getId(),
        ])->save();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        // Role-based redirect (Requirements 1.6, 1.7).
        $redirectRoute = $user->isAdmin()
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect()->intended($redirectRoute)
            ->with('success', 'Selamat datang kembali di DuitPelajar, ' . $user->name . '!');
    }

    return back()->withErrors([
        'email' => 'Email atau kata sandi yang Anda masukkan salah.',
    ])->onlyInput('email');
}
```

### Modified: CategoryController (user-facing)

Remove `store`, `update`, and `destroy` methods, keeping only the read-only `index` (Requirement 4.8). The `index` method changes to show only active categories.

```php
<?php
// app/Http/Controllers/CategoryController.php — reduced to read-only

class CategoryController extends Controller
{
    public function index(): View
    {
        $incomeCategories = Category::active()->income()->orderBy('name')->get();
        $expenseCategories = Category::active()->expense()->orderBy('name')->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }
}
```

### Modified: TransactionController

Two changes:
1. Category selection uses `Category::active()` instead of `scopeAvailableFor($user->id)` (Requirement 4.9).
2. Transaction validation rejects deactivated categories by checking `is_active = true` (Requirements 4.10, 4.11).

```php
<?php
// TransactionController — category-related changes

// In create(), edit(), and index():
$categories = Category::active()->orderBy('name')->get();

// In store() and update() validation:
'category_id' => [
    'required',
    Rule::exists('categories', 'id')->where(function ($query) {
        $query->where('is_active', true);
    }),
],
```

### New: MessageController (user-facing)

Regular users create conversations, view their own, and reply. Privacy is enforced: a user requesting another user's conversation gets 403 (Requirement 6.8).

```php
<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Message::where('user_id', Auth::id())
            ->with('replies')
            ->latest()
            ->paginate(15);

        return view('messages.index', compact('messages'));
    }

    public function create(): View
    {
        return view('messages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ], [
            'title.required' => 'Judul pesan wajib diisi.',
            'body.required' => 'Isi pesan wajib diisi.',
        ]);

        Message::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => MessageStatus::New,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Pesan Anda telah terkirim ke admin.');
    }

    public function show(Message $message): View
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesan ini.');
        }

        $message->load('replies.sender');

        return view('messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesan ini.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        MessageReply::create([
            'message_id' => $message->id,
            'sender_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Balasan terkirim.');
    }
}
```

### New: AnnouncementController (user-facing, read-only)

```php
<?php
// app/Http/Controllers/AnnouncementController.php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::visibleToUsers()->latest('published_at')->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        abort_unless(
            $announcement->status === 'published' &&
            ($announcement->published_at === null || $announcement->published_at <= now()),
            404
        );

        return view('announcements.show', compact('announcement'));
    }
}
```

### New: ArticleController (user-facing, read-only)

```php
<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::visibleToUsers()->latest('published_at')->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_unless(
            $article->status === 'published' &&
            ($article->published_at === null || $article->published_at <= now()),
            404
        );

        return view('articles.show', compact('article'));
    }
}
```

---

## Routes

### Admin route group (new)

Added to `routes/web.php` below the existing authenticated group. Middleware is applied inline (matching the existing pattern — not aliased in `bootstrap/app.php`).

```php
<?php
// routes/web.php — additions

use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminStatisticController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\EnsureUserIsAdmin;

// Inside the existing ['auth', EnsureSingleLoginSession::class] group,
// add user-facing routes for new content:

// Messages (user-facing help center)
Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');

// Announcements (user-facing, published only)
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

// Articles (user-facing, published only)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// Remove these existing user category CRUD routes (Requirement 4.8):
// Route::post('/categories', ...)->name('categories.store');       — REMOVE
// Route::put('/categories/{category}', ...)->name('categories.update'); — REMOVE
// Route::delete('/categories/{category}', ...)->name('categories.destroy'); — REMOVE
// Keep: Route::get('/categories', ...)->name('categories.index');

// Admin routes — separate group with triple middleware
Route::middleware(['auth', EnsureSingleLoginSession::class, EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::put('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::put('/categories/{category}/activate', [AdminCategoryController::class, 'activate'])->name('categories.activate');
        Route::put('/categories/{category}/deactivate', [AdminCategoryController::class, 'deactivate'])->name('categories.deactivate');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');
        Route::put('/messages/{message}/done', [AdminMessageController::class, 'markDone'])->name('messages.done');

        Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
        Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
        Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('/statistics', [AdminStatisticController::class, 'index'])->name('statistics.index');

        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('profile.photo');
    });
```

---

## Views

### Admin layout: `resources/views/layouts/admin.blade.php`

A dedicated layout separate from the existing `app.blade.php`. It mirrors the design language (Tailwind v4, Plus Jakarta Sans, indigo/blue gradient, flash messages) but has its own sidebar with the nine required navigation items in Indonesian.

```blade
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - DuitPelajar Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased flex flex-col md:flex-row bg-slate-50">
    <!-- Mobile header + drawer (responsive toggle) -->
    <!-- Desktop sidebar with 9 nav items in Indonesian: -->
    <!--   1. Dashboard          → admin.dashboard          -->
    <!--   2. Manajemen Pengguna  → admin.users.index        -->
    <!--   3. Manajemen Kategori   → admin.categories.index   -->
    <!--   4. Pengumuman           → admin.announcements.index-->
    <!--   5. Pesan dan Bantuan    → admin.messages.index     -->
    <!--   6. Artikel Edukasi      → admin.articles.index     -->
    <!--   7. Statistik Aplikasi   → admin.statistics.index   -->
    <!--   8. Profil Admin         → admin.profile.show       -->
    <!--   9. Logout               → logout (POST form)       -->

    <!-- Flash messages (success/error/info) — same pattern as app.blade.php -->
    <!-- Main content: @yield('content') -->
    <!-- Scripts: @stack('scripts') for Chart.js etc. -->
</body>
</html>
```

### Admin view files

```
resources/views/admin/
├── dashboard/
│   └── index.blade.php          — stat cards + Chart.js user growth
├── users/
│   ├── index.blade.php          — paginated table with search
│   └── show.blade.php           — user profile details (no financial data)
├── categories/
│   └── index.blade.php          — table with CRUD modals + activate/deactivate
├── announcements/
│   └── index.blade.php          — table with create/edit modals
├── messages/
│   ├── index.blade.php          — all conversations with status filter
│   └── show.blade.php           — conversation thread + reply form
├── articles/
│   └── index.blade.php          — table with create/edit modals + thumbnail
├── statistics/
│   └── index.blade.php          — charts + aggregate stat cards
└── profile/
    └── show.blade.php           — profile form + password + photo upload
```

### New user-facing view files (do not modify existing files except categories/index.blade.php)

```
resources/views/
├── messages/
│   ├── index.blade.php          — user's own conversations
│   ├── create.blade.php         — new conversation form
│   └── show.blade.php           — conversation thread + reply
├── announcements/
│   ├── index.blade.php          — published announcements list
│   └── show.blade.php           — single announcement
├── articles/
│   ├── index.blade.php          — published articles list
│   └── show.blade.php           — single article with thumbnail
└── categories/
    └── index.blade.php          — MODIFIED: read-only (remove CRUD buttons/modals)
```

The `categories/index.blade.php` modification is required by Requirement 4.8 (remove user create/update/delete). All other existing user views remain unchanged (Requirements 10.6, 12.6).

---

## Artisan Command: admin:promote

Registered in `routes/console.php` following the existing project convention. It looks up a user by email, sets the role to admin, and prints a confirmation. For a non-existent email, it prints an error and leaves all accounts unchanged.

```php
<?php
// routes/console.php — additions

use App\Models\Role;
use App\Models\User;

Artisan::command('admin:promote {email}', function () {
    $email = $this->argument('email');

    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error("Pengguna dengan email '{$email}' tidak ditemukan. Tidak ada perubahan yang dilakukan.");
        return;
    }

    $user->role = Role::Admin;
    $user->save();

    $this->info("Berhasil! Pengguna '{$user->name}' ({$user->email}) kini memiliki peran admin.");
})->purpose('Promote an existing user to the admin role');
```

---

## Security Considerations

### Mass-assignment protection (Requirement 12.2)

Every model defines an explicit `$fillable` whitelist (or `#[Fillable]` attribute). The `role` field is deliberately excluded from the User model's fillable to prevent privilege escalation — only the `admin:promote` command sets it via direct property assignment. The `is_active` field is included in fillable because it is only mutated by admin controllers behind the `EnsureUserIsAdmin` middleware.

### CSRF protection (Requirement 12.1)

All admin forms include `@csrf`. The admin layout includes a `csrf-token` meta tag. POST/PUT/DELETE routes are automatically protected by Laravel's `VerifyCsrfToken` middleware (enabled by default in the web middleware group).

### Backend validation (Requirement 12.3)

Every admin form submission is validated on the backend with `$request->validate(...)` using Indonesian error messages before any data is persisted. Invalid input returns a redirect with errors and no database change.

### Password hashing (Requirement 12.4)

The User model casts `password` as `hashed`, and the `AdminProfileController::updatePassword` explicitly calls `Hash::make()`. No plaintext password is ever stored.

### Authorization layering

The admin route group applies three middleware in sequence:
1. `auth` — rejects unauthenticated visitors (redirect to login, Requirement 1.3)
2. `EnsureSingleLoginSession` — enforces single active session (Requirement 1.8, unchanged)
3. `EnsureUserIsAdmin` — rejects non-admin users with 403 (Requirement 1.2)

### Conversation privacy (Requirements 6.8, 12.7)

The user-facing `MessageController::show` and `reply` methods check `$message->user_id !== Auth::id()` and `abort(403)` before proceeding. This is enforced at the controller level, not just at the view level.

### File upload safety

Thumbnail and profile photo uploads validate `image` MIME type and `max:2048` (2MB). Files are stored on the `public` disk. The stored path is a relative string in the database.

### No individual financial data exposure (Requirements 2.4, 3.4, 8.2)

- `AdminDashboardController` and `AdminStatisticController` query only `count()` and aggregate `sum()` across the entire `transactions` table — never filtered by a specific user.
- `AdminUserController::show` deliberately does not load or pass transaction data to the view.
- No admin view renders individual user transaction amounts or balances.

---

## Backward Compatibility

### Existing user features preserved (Requirements 12.5, 12.6)

- The existing `['auth', EnsureSingleLoginSession::class]` middleware group and all its routes remain unchanged (dashboard, transactions, budgets, savings-goals, reports, profile, logout).
- The existing `AuthController` registration and logout methods are untouched; only the `login` method gains a role-based redirect branch and an `is_active` check.
- The `EnsureSingleLoginSession` middleware is unchanged and continues to apply to both user and admin routes.
- The existing user layout (`app.blade.php`) is not modified.

### Category centralization (Requirements 4.8, 4.12, 4.14)

- The migration sets all existing `user_id` values to `null`, converting user-created categories to global. Category records are preserved (not deleted), so existing `transactions.category_id` foreign keys remain valid.
- The user-facing `CategoryController` is reduced to read-only `index`, and the corresponding `store`/`update`/`destroy` routes are removed from `routes/web.php`.
- The `categories/index.blade.php` view is updated to remove create/edit/delete buttons and modals (this is the only existing user view that changes, per Requirement 4.8).
- The `TransactionController` category selection and validation switch from `scopeAvailableFor($user->id)` (system OR owned) to `Category::active()` / `where('is_active', true)`, since all categories are now global.
- The `Category::customCategories()` relationship on the User model is retained for backward compatibility but returns empty collections after centralization.

### Non-breaking schema additions

- New columns on `users` (`role`, `is_active`, `photo`) have defaults (`user`, `true`, `null`) so existing rows remain valid.
- The `categories.is_active` column defaults to `true`, so all existing categories remain active after migration.
- New tables (`announcements`, `messages`, `message_replies`, `articles`) are additive and do not affect existing tables.

---

## Error Handling

| Scenario | Handling |
|---|---|
| Unauthenticated visitor hits `/admin/*` | `auth` middleware redirects to login (302) |
| Regular user hits `/admin/*` | `EnsureUserIsAdmin` returns 403 |
| Deactivated user attempts login | Login rejected with error message; session invalidated |
| Delete category with transactions | Redirect back with error; category preserved |
| Delete admin user account | Redirect back with error; account preserved |
| User accesses another user's conversation | 403 Forbidden |
| User accesses unpublished announcement/article | 404 Not Found |
| Invalid form input | Redirect back with validation errors; no DB change |
| Non-existent email in `admin:promote` | Error message printed; no accounts changed |
| File upload exceeds 2MB or wrong type | Validation error; no file stored |

All error messages are in Indonesian, matching the existing application convention.

---

## Correctness Properties


*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property 1: Login redirect determined by role

*For any* authenticated user with valid credentials, the post-login redirect target SHALL be the admin dashboard route when the user's role is `admin`, and the existing user dashboard route when the user's role is `user`.

**Validates: Requirements 1.6, 1.7**

### Property 2: Admin middleware blocks non-admins with 403

*For any* authenticated user whose role is not `admin` and *for any* route under the `/admin` prefix, the response SHALL be HTTP 403 Forbidden.

**Validates: Requirements 1.2, 4.1**

### Property 3: admin:promote promotes existing users

*For any* existing user account, executing `admin:promote` with that account's email SHALL set the account's role to `admin` and print a confirmation message.

**Validates: Requirements 1.4**

### Property 4: admin:promote rejects unknown emails

*For any* email that does not correspond to an existing user account, executing `admin:promote` SHALL print an error message and leave every account's role unchanged.

**Validates: Requirements 1.5**

### Property 5: Aggregate statistics match database state

*For any* database state, the dashboard and statistics page SHALL display aggregate counts (total users, active users, total transactions, income categories, expense categories, incoming messages, published announcements, published articles, help messages) that exactly match the corresponding database aggregate queries.

**Validates: Requirements 2.1, 8.1**

### Property 6: No individual financial data exposed

*For any* admin dashboard, user profile view, or statistics page render, the response SHALL NOT contain any individual user's transaction amounts or balances, regardless of database state.

**Validates: Requirements 2.4, 3.4, 8.2**

### Property 7: User list paginated at 15 per page

*For any* total number of user accounts, each non-final page of the user management list SHALL display exactly 15 records.

**Validates: Requirements 3.1**

### Property 8: User search filters by name and email

*For any* search query fragment, every user returned by the user management search SHALL have a name or email containing the submitted fragment.

**Validates: Requirements 3.2**

### Property 9: User activation toggles is_active

*For any* user account, activating an inactive account SHALL set `is_active` to `true`, and deactivating an active account SHALL set `is_active` to `false`.

**Validates: Requirements 3.5, 3.6**

### Property 10: Deactivated accounts cannot log in

*For any* user account with `is_active` set to `false`, a login attempt with valid credentials SHALL be rejected with an error message and SHALL NOT establish an authenticated session.

**Validates: Requirements 3.7**

### Property 11: User deletion outcome determined by role

*For any* user account whose role is `admin`, a deletion request SHALL be prevented and the account SHALL remain in the database; *for any* user account whose role is `user`, a deletion request SHALL remove the account record from the database.

**Validates: Requirements 3.8, 3.9**

### Property 12: Deactivation preserves account data

*For any* user account, after deactivation every stored field except `is_active` SHALL remain unchanged so the account can be reactivated later.

**Validates: Requirements 3.10**

### Property 13: Category create and update persist all fields

*For any* valid category input (name, type, icon, color, is_active), creating or updating a category SHALL persist all submitted fields to the database record.

**Validates: Requirements 4.2, 4.3**

### Property 14: Category is_active toggle

*For any* category, activating it SHALL set `is_active` to `true` and deactivating it SHALL set `is_active` to `false`.

**Validates: Requirements 4.4, 4.5**

### Property 15: Category deletion gated by transaction count

*For any* category, a deletion request SHALL succeed if and only if the category has zero associated transactions; if the category has one or more transactions, the deletion SHALL be prevented with an error message.

**Validates: Requirements 4.6, 4.7**

### Property 16: Transaction forms show only active categories and enforce active selection

*For any* transaction create or edit form, the category selector SHALL present only active categories; *for any* submitted transaction referencing a deactivated category, the submission SHALL be rejected with a validation error; *for any* submitted transaction referencing an active category, the submission SHALL be accepted and persisted.

**Validates: Requirements 4.9, 4.10, 4.11**

### Property 17: Converted category references remain intact

*For any* transaction whose category was converted from a user-created category to a global category, the transaction's `category_id` foreign key SHALL still resolve to the original category record and the category SHALL continue to display correctly.

**Validates: Requirements 4.12**

### Property 18: Migration converts user categories to global

*For any* user-created category (with a non-null `user_id`), running the category migration SHALL set `user_id` to `null` while preserving the category record and its ID.

**Validates: Requirements 4.14**

### Property 19: Announcement CRUD persistence

*For any* valid announcement input, creating an announcement SHALL persist the title, body, status, author_id, and published_at; updating SHALL persist the submitted changes; deleting SHALL remove the record from the database.

**Validates: Requirements 5.1, 5.2, 5.3**

### Property 20: Announcement visibility by status and date

*For any* announcement, it SHALL be visible to regular users if and only if its status is `published` and its `published_at` is null or in the past or present; if the status is `draft`, or the status is `published` with a future `published_at`, the announcement SHALL be hidden from regular users.

**Validates: Requirements 5.4, 5.5, 5.6**

### Property 21: Conversation creation sets status to new

*For any* regular user submitting a valid message with a title and body, the system SHALL create a conversation owned by that user with a status of `new`.

**Validates: Requirements 6.1**

### Property 22: Conversation ownership privacy

*For any* regular user, the message center SHALL list only conversations owned by that user; *for any* request by a regular user to access a conversation owned by a different user, the system SHALL respond with HTTP 403 Forbidden.

**Validates: Requirements 6.2, 6.8, 12.7**

### Property 23: Admin sees all conversations

*For any* admin user opening the admin message center, the system SHALL list all conversations across all regular users regardless of ownership.

**Validates: Requirements 6.4**

### Property 24: Admin reply sets conversation status to processing

*For any* conversation and *for any* admin reply submitted to that conversation, the system SHALL store the reply with the admin recorded as sender and SHALL set the conversation status to `processing`.

**Validates: Requirements 6.5**

### Property 25: Conversation replies in chronological order

*For any* conversation with replies, the system SHALL display the original message and all replies ordered by `created_at` ascending.

**Validates: Requirements 6.3**

### Property 26: Conversation status done is persisted

*For any* conversation, when an admin sets its status to `done`, the system SHALL update and persist the status as `done`.

**Validates: Requirements 6.7**

### Property 27: Article CRUD persistence

*For any* valid article input, creating an article SHALL persist the title, slug, thumbnail, content, category, status, author_id, and published_at; updating SHALL persist the submitted changes; deleting SHALL remove the record from the database.

**Validates: Requirements 7.1, 7.2, 7.3**

### Property 28: Article visibility by status and date

*For any* article, it SHALL be visible to regular users if and only if its status is `published` and its `published_at` is null or in the past or present; if the status is `draft`, the article SHALL be hidden from regular users.

**Validates: Requirements 7.4, 7.5**

### Property 29: File upload stores and records path

*For any* valid image upload as an article thumbnail or admin profile photo, the system SHALL store the image file and record its stored path on the corresponding database record.

**Validates: Requirements 7.6, 9.4**

### Property 30: Admin profile updates persist changes

*For any* valid name or email update submitted by an admin, the system SHALL validate and persist the changes to the admin's account record.

**Validates: Requirements 9.2**

### Property 31: Password hashing on update

*For any* valid password update, the system SHALL hash the new password using the existing hashing mechanism and persist it; the stored value SHALL verify against the plaintext and SHALL NOT equal the plaintext.

**Validates: Requirements 9.3, 12.4**

### Property 32: CSRF tokens on all admin forms

*For any* admin form rendered by the AdminPanel, the form SHALL include a CSRF token field.

**Validates: Requirements 12.1**

### Property 33: Fillable mass-assignment protection on every model

*For any* model class in the application (existing or new), the model SHALL define an explicit `$fillable` whitelist or `#[Fillable]` attribute so that mass-assignment is restricted to whitelisted fields only.

**Validates: Requirements 12.2**

### Property 34: Backend validation before persist

*For any* AdminPanel form submission with invalid input, the system SHALL reject the submission with a validation error and SHALL NOT persist any data to the database.

**Validates: Requirements 12.3**

### Property 35: Migration reversibility

*For any* new migration in this feature, executing the `up` method followed by the `down` method SHALL restore the schema to its prior state (added tables removed, added columns dropped).

**Validates: Requirements 11.9**

### Property 36: Existing user views unchanged

*For any* existing user-facing view file other than `categories/index.blade.php`, the file's content SHALL remain unchanged after the admin-dashboard feature is implemented.

**Validates: Requirements 10.6, 12.6**

---

## Testing Strategy

The test suite uses PHPUnit (via `php artisan test`) with Laravel's feature test facilities. Tests are created with `php artisan make:test` and use model factories for test data.

**Dual testing approach:**

- **Property tests** validate the universal properties above across randomized inputs (minimum 100 iterations per property where applicable). Generators create random users, categories, transactions, announcements, articles, and conversations, then assert the property holds for each generated case. Property tests are tagged with `Feature: admin-dashboard, Property N: <text>`.

- **Unit/example tests** cover specific scenarios and edge cases: the admin login redirect example, unauthenticated redirect, single-session enforcement for admins, sidebar navigation order, deactivated account rejection, admin self-deletion prevention, and backward compatibility regression (existing user routes still functional).

- **Smoke tests** verify one-time configuration: migration schema correctness (columns, types, defaults, foreign keys, indexes), admin layout existence, and middleware application on the admin route group.

Test files are organized by feature area:

```
tests/Feature/Admin/
├── AuthorizationTest.php        — Properties 1-4 (access control, promote)
├── DashboardTest.php            — Properties 5-6 (aggregate stats, no financial data)
├── UserManagementTest.php       — Properties 7-12 (pagination, search, activate, delete)
├── CategoryManagementTest.php  — Properties 13-18 (CRUD, toggle, delete gate, conversion)
├── AnnouncementTest.php         — Properties 19-20 (CRUD, visibility)
├── MessageCenterTest.php        — Properties 21-26 (conversations, privacy, status)
├── ArticleTest.php             — Properties 27-29 (CRUD, visibility, upload)
├── AdminProfileTest.php         — Properties 30-31 (update, password hashing)
├── SecurityTest.php             — Properties 32-34 (CSRF, fillable, validation)
├── MigrationTest.php            — Properties 35 (reversibility), schema smoke tests
└── BackwardCompatTest.php       — Property 36 (existing views unchanged), regression
```

Each property test runs a minimum of 100 iterations. Unit tests use concrete examples. The full suite must pass with zero errors and zero failing assertions (Requirement 13.14).
