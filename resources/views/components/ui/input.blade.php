{{-- Input Component --}}
{{-- Props: label, name, type (text|email|password|number|tel), value, error, required, placeholder --}}
@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'error' => '',
    'required' => false,
    'placeholder' => '',
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        class="form-input {{ $error ? 'error' : '' }}"
    />
    @if ($error)
        <p class="mt-1.5 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
