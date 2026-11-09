<?php

use App\Http\Controllers\AdminActivityLogController;
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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\EnsureSingleLoginSession;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsNotAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root: Landing Page for guests, smart redirect for authenticated users
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }

    return view('landing');
})->name('home');

// Guest authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout — available to all authenticated users (both admin and regular users)
Route::middleware(['auth', EnsureSingleLoginSession::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated protected routes (user-only — admins are redirected to admin.dashboard on login)
Route::middleware(['auth', EnsureSingleLoginSession::class, EnsureUserIsNotAdmin::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Categories (read + user custom category management)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Savings Goals
    Route::get('/savings-goals', [SavingsGoalController::class, 'index'])->name('savings-goals.index');
    Route::post('/savings-goals', [SavingsGoalController::class, 'store'])->name('savings-goals.store');
    Route::put('/savings-goals/{savingsGoal}', [SavingsGoalController::class, 'update'])->name('savings-goals.update');
    Route::post('/savings-goals/{savingsGoal}/deposit', [SavingsGoalController::class, 'deposit'])->name('savings-goals.deposit');
    Route::delete('/savings-goals/{savingsGoal}', [SavingsGoalController::class, 'destroy'])->name('savings-goals.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

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

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

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

        Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    });
