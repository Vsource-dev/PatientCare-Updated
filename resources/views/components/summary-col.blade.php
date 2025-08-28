@props(['label', 'value'])

<div {{ $attributes->merge(['class' => 'col-sm-6 col-lg-4 mb-3']) }}>
    <small class="text-muted">{{ $label }}</small>
    <div class="fw-semibold">{{ $value }}</div>
</div>