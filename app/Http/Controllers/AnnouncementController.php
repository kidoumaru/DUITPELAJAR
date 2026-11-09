<?php

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
        // Gunakan scope yang sama dengan index agar logika konsisten
        // (status published DAN published_at <= now() berdasarkan timezone DB).
        abort_unless(
            Announcement::visibleToUsers()->where('id', $announcement->id)->exists(),
            404
        );

        return view('announcements.show', compact('announcement'));
    }
}
