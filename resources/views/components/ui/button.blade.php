{{-- Button Component --}}
{{-- Props: variant (primary|danger|ghost|success), type (submit|button), href, size (sm|md|lg) --}}
@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
])

@php
$variantClasses = [
    'primary' => 'bg-gray-900 hover:bg-gray-800 text-white',
    'danger'  => 'bg-red-600 hover:bg-red-700 text-white',
    'ghost'   => 'bg-transparent hover:bg-gray-100 text-gray-700',
    'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 disabled:opacity-50 disabled:cursor-not-allowed';

$classes = trim($baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']));
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ $slot }}</a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ $slot }}</button>
@endif
