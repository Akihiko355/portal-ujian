{{-- Filter Bar Component --}}
{{-- Props: action, method (GET default), searchPlaceholder --}}
@props([
    'action' => '#',
    'method' => 'GET',
    'searchPlaceholder' => 'Cari...',
])

@php
$method = strtoupper($method) === 'POST' ? 'POST' : 'GET';
@endphp

<form
    action="{{ $action }}"
    method="{{ $method }}"
    class="flex flex-wrap gap-2 items-end"
>
    @csrf
    @if ($method !== 'POST')
        @method('GET')
    @endif

    <div class="flex-1 min-w-[200px]">
        <input
            type="text"
            name="search"
            value="{{ old('search') }}"
            placeholder="{{ $searchPlaceholder }}"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
        />
    </div>

    {{-- Additional filter slot --}}
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap gap-2 items-end">
            {{ $slot }}
        </div>
    @endif

    <button
        type="submit"
        class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
    >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        Filter
    </button>
</form>
