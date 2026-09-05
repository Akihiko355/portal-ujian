{{-- Page Header Component --}}
{{-- Props: title, backRoute (optional) --}}
@props([
    'title' => '',
    'backRoute' => null,
])

<div class="page-header">
    <div class="flex items-center gap-3 min-w-0">
        @if ($backRoute)
            <a href="{{ $backRoute }}"
               class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
        @endif
        <h1 class="text-xl font-bold text-slate-900 truncate">{{ $title }}</h1>
    </div>

    @if (isset($action) && $action->isNotEmpty())
        <div class="flex items-center gap-2 flex-shrink-0">
            {{ $action }}
        </div>
    @endif
</div>
