<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FailedLoginAttempt;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'activity');

        if ($tab === 'failed') {
            $query = FailedLoginAttempt::query();

            if ($request->filled('guard_type')) {
                $query->where('guard_type', $request->guard_type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->where('attempted_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('attempted_at', '<=', $request->date_to . ' 23:59:59');
            }

            $logs = $query->orderBy('attempted_at', 'desc')->paginate(20);
            $logType = 'failed';
        } else {
            $query = ActivityLog::query()->with('admin');

            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            if ($request->filled('model_type')) {
                $query->where('model_type', $request->model_type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('model_label', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate(20);
            $logType = 'activity';
        }

        return view('admin.logs.index', compact('logs', 'logType'));
    }
}
