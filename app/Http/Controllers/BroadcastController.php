<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastReceipt;
use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    public function index(Request $request)
    {
        $query = Broadcast::with(['admin'])->latest();

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        $broadcasts = $query->paginate(15);

        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $departments = Department::all();
        $examSchedules = ExamSchedule::with(['subject', 'department'])
            ->active()
            ->latest()
            ->get();

        return view('admin.broadcasts.create', compact('departments', 'examSchedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string|max:2000',
            'urgency' => 'required|in:info,warning,important',
            'target_type' => 'required|in:all,department,exam_schedule',
            'target_ids' => 'required_if:target_type,department,exam_schedule|array',
            'send_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:send_at',
        ]);

        $broadcast = Broadcast::create([
            'admin_id' => Auth::guard('admin')->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'urgency' => $validated['urgency'],
            'target_type' => $validated['target_type'],
            'target_ids' => in_array($validated['target_type'], ['department', 'exam_schedule'])
                ? $validated['target_ids']
                : null,
            'send_at' => $validated['send_at'] ?? now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'created_at' => now(),
        ]);

        // Deliver immediately (for v1, no scheduling)
        $this->deliverBroadcast($broadcast);

        return redirect()->route('admin.broadcasts.index')
            ->with('success', "Broadcast '{$broadcast->title}' berhasil dikirim ke {$broadcast->delivery_count} mahasiswa.");
    }

    public function show(Broadcast $broadcast)
    {
        $broadcast->load(['admin', 'receipts.user']);
        $readCount = $broadcast->receipts()->whereNotNull('read_at')->count();
        $total = $broadcast->receipts()->count();

        return view('admin.broadcasts.show', compact('broadcast', 'readCount', 'total'));
    }

    public function destroy(Broadcast $broadcast)
    {
        $broadcast->delete();
        return redirect()->route('admin.broadcasts.index')->with('success', 'Broadcast dihapus.');
    }

    public function recipientCount(Request $request)
    {
        $targetType = $request->get('target_type', 'all');
        $targetIds = $request->get('target_ids', []);

        $query = User::where('is_active', true);

        if ($targetType === 'department' && !empty($targetIds)) {
            $query->whereIn('department_id', $targetIds);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }

    protected function deliverBroadcast(Broadcast $broadcast): void
    {
        $query = User::where('is_active', true);

        if ($broadcast->target_type === 'department' && !empty($broadcast->target_ids)) {
            $query->whereIn('department_id', $broadcast->target_ids);
        }

        $users = $query->get();

        $receipts = $users->map(fn($user) => [
            'broadcast_id' => $broadcast->id,
            'user_id' => $user->id,
            'dismissed' => false,
            'created_at' => now(),
        ])->toArray();

        // Batch insert for efficiency
        foreach (array_chunk($receipts, 100) as $chunk) {
            BroadcastReceipt::insert($chunk);
        }
    }
}
