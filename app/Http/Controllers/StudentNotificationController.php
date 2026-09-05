<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastReceipt;
use App\Models\User;
use Illuminate\Http\Request;

class StudentNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('web')->user();

        // Get active broadcasts for this user
        $broadcasts = Broadcast::with(['admin'])
            ->active()
            ->sent()
            ->forUser($user)
            ->with(['receipts' => fn($q) => $q->where('user_id', $user->id)])
            ->latest()
            ->paginate(20);

        return view('user.notifications.index', compact('broadcasts'));
    }

    public function markBroadcastRead(Broadcast $broadcast)
    {
        $user = auth()->guard('web')->user();

        $receipt = BroadcastReceipt::where('broadcast_id', $broadcast->id)
            ->where('user_id', $user->id)
            ->first();

        if ($receipt) {
            $receipt->markAsRead();
        }

        return redirect()->back();
    }

    public function dismissBroadcast(Request $request)
    {
        $request->validate([
            'broadcast_id' => 'required|exists:broadcasts,id',
        ]);

        $user = auth()->guard('web')->user();

        $receipt = BroadcastReceipt::where('broadcast_id', $request->broadcast_id)
            ->where('user_id', $user->id)
            ->first();

        if ($receipt) {
            $receipt->dismiss();
        }

        return response()->json(['success' => true]);
    }

    public function recent()
    {
        $user = auth()->guard('web')->user();

        $broadcasts = Broadcast::active()
            ->sent()
            ->forUser($user)
            ->with(['receipts' => fn($q) => $q->where('user_id', $user->id)])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'urgency' => $b->urgency,
                'is_read' => $b->receipts->first()?->read_at !== null,
                'is_dismissed' => $b->receipts->first()?->dismissed ?? false,
                'expires_at' => $b->expires_at?->toISOString(),
                'created_at' => $b->created_at->toISOString(),
            ]);

        return response()->json([
            'broadcasts' => $broadcasts,
            'unread_count' => $broadcasts->where('is_read', false)->where('is_dismissed', false)->count(),
        ]);
    }
}
