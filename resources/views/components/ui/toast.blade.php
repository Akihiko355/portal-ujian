{{-- Toast Component --}}
{{-- Props: message, type (success|error|warning|info) --}}
@props([
    'message' => '',
    'type' => 'info',
])

@php
$styleMap = [
    'success' => [
        'border' => 'border-emerald-200',
        'bg'     => 'bg-emerald-50',
        'text'   => 'text-emerald-800',
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
    ],
    'error' => [
        'border' => 'border-red-200',
        'bg'     => 'bg-red-50',
        'text'   => 'text-red-800',
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
    ],
    'warning' => [
        'border' => 'border-amber-200',
        'bg'     => 'bg-amber-50',
        'text'   => 'text-amber-800',
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    ],
    'info' => [
        'border' => 'border-blue-200',
        'bg'     => 'bg-blue-50',
        'text'   => 'text-blue-800',
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
];

$style = $styleMap[$type] ?? $styleMap['info'];
@endphp

<div
    class="rounded-xl border px-4 py-3 shadow-lg flex items-center gap-3 {{ $style['bg'] }} {{ $style['text'] }}"
    role="alert"
>
    <svg
        class="w-5 h-5 flex-shrink-0"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
    >
        {!! $style['icon'] !!}
    </svg>
    <p class="text-sm font-medium">{{ $message }}</p>
</div>
