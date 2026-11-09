<?php

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

        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userGrowth[] = [
                'label' => $date->locale('id')->translatedFormat('M Y'),
                'count' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        }

        $usersByPeriod = User::selectRaw(
            "DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total"
        )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
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
