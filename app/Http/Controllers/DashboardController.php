<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastReceipt;
use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (request()->is('admin/*')) {
            $counts = DB::select("
                SELECT
                    (SELECT COUNT(*) FROM departments) AS departments,
                    (SELECT COUNT(*) FROM subjects) AS subjects,
                    (SELECT COUNT(*) FROM users) AS users,
                    (SELECT COUNT(*) FROM exam_schedules) AS exam_schedules,
                    (SELECT COUNT(*) FROM scores WHERE is_published = 1) AS scores_published,
                    (SELECT COUNT(*) FROM scores WHERE is_published = 0) AS scores_pending
            ");
            $stats = (array) $counts[0];
            return view('admin.dashboard', compact('stats'));
        }

        $user = Auth::user()->load('department', 'scores.subject');
        $examSchedules = ExamSchedule::with(['subject', 'department'])
            ->where('is_published', true)
            ->when($user->department_id, fn($q) => $q->where('department_id', $user->department_id))
            ->orderBy('exam_start_datetime')
            ->get();
        $publishedScores = $user->scores()->where('is_published', true)->with('subject')->get();

        $unreadBroadcasts = Broadcast::active()
            ->sent()
            ->forUser($user)
            ->whereDoesntHave('receipts', fn($q) => $q->where('user_id', $user->id)->where('read_at', '!=', null))
            ->with(['receipts' => fn($q) => $q->where('user_id', $user->id)])
            ->limit(3)
            ->get();

        return view('user.dashboard', compact('user', 'examSchedules', 'publishedScores', 'unreadBroadcasts'));
    }
}
