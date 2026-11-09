<?php

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
