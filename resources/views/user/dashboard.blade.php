@extends('layouts.user')

@section('content')
<x-ui.page-header title="Hai, {{ $user->name }}!" description="Selamat datang di Portal Ujian" />

<!-- Broadcast Banners -->
@if($unreadBroadcasts->isNotEmpty())
    @foreach($unreadBroadcasts as $broadcast)
        @php
            $receipt = $broadcast->receipts->first();
            $isRead = $receipt?->read_at !== null;
        @endphp
        @if(!$isRead)
        <div class="mb-6 p-4 rounded-xl border {{ $broadcast->urgency === 'important' ? 'bg-red-50 border-red-200' : ($broadcast->urgency === 'warning' ? 'bg-amber-50 border-amber-200' : 'bg-blue-50 border-blue-200') }}">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    @if($broadcast->urgency === 'important')
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif($broadcast->urgency === 'warning')
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold {{ $broadcast->urgency === 'important' ? 'text-red-800' : ($broadcast->urgency === 'warning' ? 'text-amber-800' : 'text-blue-800') }}">{{ $broadcast->title }}</p>
                    <p class="text-sm {{ $broadcast->urgency === 'important' ? 'text-red-700' : ($broadcast->urgency === 'warning' ? 'text-amber-700' : 'text-blue-700') }} mt-0.5 line-clamp-2">{{ $broadcast->content }}</p>
                    <div class="flex items-center gap-4 mt-2">
                        <form method="POST" action="{{ route('user.notifications.read', $broadcast) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-blue-700 hover:text-blue-800 underline">Tandai dibaca</button>
                        </form>
                        <a href="{{ route('user.notifications') }}" class="text-xs text-blue-700 hover:text-blue-800 underline">Lihat semua</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif

<!-- Exam Schedules -->
<div class="card overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700">Jadwal Ujian</h2>
    </div>
    @if($examSchedules->isEmpty())
        <div class="p-8"><x-ui.empty-state message="Belum ada jadwal ujian yang dipublikasikan" /></div>
    @else
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th class="hidden md:table-cell">Nomor</th>
                    <th>Briefing</th>
                    <th>Mulai</th>
                    <th class="hidden sm:table-cell">Selesai</th>
                    <th>Link</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examSchedules as $schedule)
                <tr>
                    <td class="font-medium text-slate-900">{{ $schedule->subject->name }}</td>
                    <td class="hidden md:table-cell">
                        <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $schedule->exam_number ?: '-' }}</span>
                    </td>
                    <td>
                        <div class="text-sm text-slate-700">{{ $schedule->briefing_datetime->format('d M Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $schedule->briefing_datetime->format('H:i') }}</div>
                    </td>
                    <td class="font-semibold text-emerald-600">{{ $schedule->exam_start_datetime->format('d M Y H:i') }}</td>
                    <td class="hidden sm:table-cell font-medium text-red-500">{{ $schedule->exam_end_datetime->format('d M Y H:i') }}</td>
                    <td>
                        @if($schedule->exam_link && $schedule->isLinkVisible())
                            <a href="{{ $schedule->exam_link }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">Buka</a>
                        @else
                            <span class="text-slate-300 text-sm">-</span>
                        @endif
                    </td>
                    <td>
                        @if($schedule->isPasswordVisible())
                            <span class="font-mono text-sm bg-slate-100 text-slate-700 px-2 py-1 rounded">{{ $schedule->exam_password }}</span>
                        @else
                            <span class="text-slate-300 text-sm">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Scores -->
<div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700">Nilai Saya</h2>
    </div>
    @if($publishedScores->isEmpty())
        <div class="p-8"><x-ui.empty-state message="Belum ada nilai yang dipublikasikan" /></div>
    @else
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th>Nilai</th>
                    <th class="hidden sm:table-cell">Passing</th>
                    <th>Status</th>
                    <th class="hidden lg:table-cell">Dipublikasikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($publishedScores as $score)
                <tr>
                    <td class="font-medium text-slate-900">{{ $score->subject->name }}</td>
                    <td><span class="text-xl font-bold text-slate-900">{{ $score->score }}</span></td>
                    <td class="hidden sm:table-cell text-slate-500">{{ $score->subject->passing_grade }}</td>
                    <td>
                        <x-ui.badge :type="$score->isPassed() ? 'success' : 'danger'" :label="$score->isPassed() ? 'LULUS' : 'TIDAK LULUS'" />
                    </td>
                    <td class="hidden lg:table-cell text-slate-500 text-xs">{{ $score->published_at?->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
