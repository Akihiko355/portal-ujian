{{-- Select Component --}}
{{-- Props: label, name, options (array), selected, error, required --}}
@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'selected' => '',
    'error' => '',
    'required' => false,
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
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        class="form-select {{ $error ? 'error' : '' }}"
    >
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="mt-1.5 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
