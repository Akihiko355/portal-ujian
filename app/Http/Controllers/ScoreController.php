<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoreRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Score::with(['user', 'subject', 'inputByAdmin']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "{$search}%")
                  ->orWhere('email', $search);
            });
        }

        $scores = $query->latest()->paginate(15);
        $subjects = Subject::all();

        return view('admin.scores.index', compact('scores', 'subjects'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get();
        $subjects = Subject::with('departments')->get();
        $departments = Department::all();

        $subjectsJson = $subjects->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'passing_grade' => $s->passing_grade,
            'departments' => $s->departments->pluck('id')->toArray(),
        ])->values()->toJson();

        $deptMapJson = $departments->pluck('name', 'id')->toJson();

        return view('admin.scores.create', compact('users', 'subjects', 'departments', 'subjectsJson', 'deptMapJson'));
    }

    public function store(StoreScoreRequest $request)
    {
        $validated = $request->validated();
        $userId = $validated['user_id'];
        $adminId = Auth::guard('admin')->id();
        $created = 0;
        $skipped = 0;

        foreach ($validated['scores'] as $entry) {
            $existing = Score::where('user_id', $userId)
                ->where('subject_id', $entry['subject_id'])
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            Score::create([
                'user_id' => $userId,
                'subject_id' => $entry['subject_id'],
                'score' => $entry['score'],
                'input_by_admin_id' => $adminId,
                'is_published' => false,
            ]);

            $created++;
        }

        $message = "{$created} skor berhasil ditambahkan.";
        if ($skipped > 0) {
            $message .= " {$skipped} skor dilewati (sudah ada).";
        }

        return redirect()->route('admin.scores.index')->with('success', $message);
    }

    public function edit(Score $score)
    {
        $users = User::where('is_active', true)->get();
        $subjects = Subject::all();
        return view('admin.scores.edit', compact('score', 'users', 'subjects'));
    }

    public function update(Request $request, Score $score)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
        ]);

        $score->update($validated);

        return redirect()->route('admin.scores.index')->with('success', 'Skor berhasil diupdate.');
    }

    public function destroy(Score $score)
    {
        $score->delete();
        return redirect()->route('admin.scores.index')->with('success', 'Skor berhasil dihapus.');
    }

    public function publish(Score $score)
    {
        $score->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'published',
            'model_type' => Score::class,
            'model_id' => $score->id,
            'model_label' => "Score: {$score->user->name} - {$score->subject->name} ({$score->score})",
            'created_at' => now(),
        ]);

        return redirect()->route('admin.scores.index')->with('success', 'Skor berhasil dipublikasikan.');
    }

    public function unpublish(Score $score)
    {
        $score->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'unpublished',
            'model_type' => Score::class,
            'model_id' => $score->id,
            'model_label' => "Score: {$score->user->name} - {$score->subject->name} ({$score->score})",
            'created_at' => now(),
        ]);

        return redirect()->route('admin.scores.index')->with('success', 'Skor berhasil di-unpublish.');
    }

    public function publishAll()
    {
        $count = Score::where('is_published', false)
            ->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'bulk_action',
            'model_label' => "Published {$count} scores",
            'metadata' => ['action' => 'publish_all', 'count' => $count],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.scores.index')->with('success', "{$count} skor berhasil dipublikasikan semua.");
    }

    public function unpublishAll()
    {
        $count = Score::where('is_published', true)
            ->update([
                'is_published' => false,
                'published_at' => null,
            ]);

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => 'bulk_action',
            'model_label' => "Unpublished {$count} scores",
            'metadata' => ['action' => 'unpublish_all', 'count' => $count],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.scores.index')->with('success', "{$count} skor berhasil di-unpublish semua.");
    }
}
