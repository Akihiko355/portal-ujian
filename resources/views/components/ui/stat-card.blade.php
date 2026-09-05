{{-- Stat Card Component --}}
@props([
    'label' => '',
    'value' => '',
    'icon' => '',
    'color' => 'slate',
])

<div class="stat-card">
    <div class="flex items-start justify-between">
        <div>
            <p class="stat-card-label">{{ $label }}</p>
            <p class="stat-card-value mt-1">{{ $value }}</p>
        </div>
        @if ($icon)
            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
