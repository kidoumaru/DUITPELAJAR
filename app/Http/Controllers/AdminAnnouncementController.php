<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\Announcement;
use App\Models\AnnouncementStatus;
use App\Models\User;
use App\Models\UserNotification;
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

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => AnnouncementStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
            'author_id' => $request->user()->id,
        ]);

        AdminActivityLog::record(
            $request->user()->id,
            'published',
            'announcement',
            $announcement->id,
            $announcement->title,
            "Menerbitkan pengumuman: \"{$announcement->title}\" (status: {$validated['status']})"
        );

        // Send notifications to all active non-admin users when published.
        if ($announcement->status === AnnouncementStatus::Published) {
            $this->notifyUsersNewAnnouncement($announcement);
        }

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

        $wasPublished = $announcement->status === AnnouncementStatus::Published;
        $willBePublished = $validated['status'] === 'published';

        $announcement->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => AnnouncementStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
        ]);

        AdminActivityLog::record(
            $request->user()->id,
            'updated',
            'announcement',
            $announcement->id,
            $announcement->title,
            "Memperbarui pengumuman: \"{$announcement->title}\" (status: {$validated['status']})"
        );

        // Send notification only when transitioning from draft → published.
        if (! $wasPublished && $willBePublished) {
            $this->notifyUsersNewAnnouncement($announcement);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        AdminActivityLog::record(
            request()->user()->id,
            'deleted',
            'announcement',
            $announcement->id,
            $announcement->title,
            "Menghapus pengumuman: \"{$announcement->title}\""
        );

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Create a notification for every active non-admin user about a new announcement.
     */
    private function notifyUsersNewAnnouncement(Announcement $announcement): void
    {
        $userIds = User::where('is_active', true)
            ->where('role', '!=', 'admin')
            ->pluck('id');

        $now = now();
        $notifications = $userIds->map(fn ($userId) => [
            'user_id' => $userId,
            'type' => 'announcement_new',
            'title' => 'Pengumuman Baru: ' . $announcement->title,
            'body' => mb_substr(strip_tags($announcement->body), 0, 200),
            'url' => route('announcements.show', $announcement),
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Batch insert in chunks to avoid memory issues with large user bases.
        foreach (array_chunk($notifications, 500) as $chunk) {
            UserNotification::insert($chunk);
        }
    }
}
