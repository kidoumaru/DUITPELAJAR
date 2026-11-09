<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdminActivityLog::with('admin')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->input('subject_type'));
        }

        $logs = $query->paginate(30)->withQueryString();

        $actions = ['created', 'updated', 'activated', 'deactivated', 'deleted', 'published'];
        $subjectTypes = ['category', 'announcement'];

        return view('admin.activity_logs.index', compact('logs', 'actions', 'subjectTypes'));
    }
}
