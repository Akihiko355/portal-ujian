@extends('layouts.user')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pesan dari admin</p>
        </div>
    </div>

    @if($broadcasts->isEmpty())
        <div class="card py-16 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm text-slate-400 font-medium">Tidak ada notifikasi</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($broadcasts as $broadcast)
                @php
                    $receipt = $broadcast->receipts->first();
                    $isRead = $receipt?->read_at !== null;
                    $isDismissed = $receipt?->dismissed ?? false;
                @endphp

                @if(!$isDismissed)
                <div class="card {{ $broadcast->urgency === 'important' ? 'border-red-200 bg-red-50/30' : ($broadcast->urgency === 'warning' ? 'border-amber-200 bg-amber-50/30' : '') }} {{ !$isRead ? 'ring-1 ring-blue-200' : '' }}">
                    <div class="flex items-start gap-3">
                        <!-- Urgency indicator -->
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($broadcast->urgency === 'important') bg-red-100 text-red-600
                            @elseif($broadcast->urgency === 'warning') bg-amber-100 text-amber-600
                            @else bg-blue-100 text-blue-600
                            @endif">
                            @if($broadcast->urgency === 'important')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            @elseif($broadcast->urgency === 'warning')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $broadcast->title }}</h3>
                                @if(!$isRead)
                                    <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                                @endif
                            </div>
                            <div class="prose prose-sm max-w-none text-slate-600 whitespace-pre-wrap">{{ $broadcast->content }}</div>
                            <div class="flex items-center gap-4 mt-3">
                                <span class="text-[11px] text-slate-400">{{ $broadcast->created_at->format('d M Y, H:i') }}</span>
                                @if(!$isRead)
                                    <form method="POST" action="{{ route('user.notifications.read', $broadcast) }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-blue-600 hover:underline">Tandai sudah dibaca</button>
                                    </form>
                                @endif
                                <button onclick="dismissNotif({{ $broadcast->id }})" class="text-[11px] text-slate-400 hover:text-slate-600">Sembunyikan</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if($broadcasts->hasPages())
            <div class="mt-5">
                {{ $broadcasts->withQueryString()->links() }}
            </div>
        @endif
    @endif
@endsection
