{{-- Empty State Component --}}
@props([
    'message' => 'Belum ada data',
    'description' => '',
])

<div class="empty-state">
    <div class="empty-state-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
    </div>
    <p class="empty-state-text font-medium text-slate-600 mb-1">{{ $message }}</p>
    @if ($description)
        <p class="text-xs text-slate-400">{{ $description }}</p>
    @endif
</div>
