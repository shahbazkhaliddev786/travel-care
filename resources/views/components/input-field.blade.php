<div class="field-box">
    {{-- Agar Label Mojood Hai --}}
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif

    <div class="input-group @error($name) error-border @enderror {{ $extraClass }}" {{ $id ? "id=$id" : '' }}>
        {{-- Agar Select Box Mojood Hai --}}
        @if($hasSelect)
            <select class="country-code" name="{{ $name }}_code">
                @foreach($options as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            <span>|</span>
        @endif

        {{-- Agar Icon Mojood Hai --}}
        @if($icon)
            <img src="{{ asset($icon) }}" alt="{{ $name }}" class="input-icon">
        @endif

        {{-- Input Field with Pattern --}}
        <input type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" 
            {{ $pattern ? "pattern=$pattern" : '' }} required>
    </div>

    {{-- Laravel Validation Error --}}
    @error($name)
        <span class="error-message">{{ $message }}</span>
    @enderror
</div>
