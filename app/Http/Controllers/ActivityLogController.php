<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $activityLogs = $query->paginate(25)->withQueryString();
        $pageTitle = 'Activity History';
        $routeName = 'admin.activity_logs.index';

        return view('backend.activity_logs.index', compact('activityLogs', 'pageTitle', 'routeName'));
    }

    public function myActivity(Request $request)
    {
        $query = ActivityLog::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $activityLogs = $query->paginate(25)->withQueryString();
        $pageTitle = 'My Activity';
        $routeName = 'my_activity.index';

        return view('backend.activity_logs.index', compact('activityLogs', 'pageTitle', 'routeName'));
    }
}
