{{-- Badge Component --}}
{{-- Props: type (success|warning|danger|info|neutral), label --}}
@props([
    'type' => 'neutral',
    'label' => '',
])

<span class="badge badge-{{ $type }}">
    {{ $label ?: $slot }}
</span>
