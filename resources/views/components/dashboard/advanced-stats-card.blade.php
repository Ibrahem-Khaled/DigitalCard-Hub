{{-- مكون بطاقة الإحصائيات المتقدمة --}}
@props([
    'title' => '',
    'value' => '',
    'label' => '',
    'icon' => '',
    'change' => null,
    'changeType' => 'neutral',
    'changeText' => '',
    'trend' => null
])

<div class="stats-card-advanced">
    <div class="stats-card-header">
        <h3 class="stats-card-title">{{ $title }}</h3>
        <i class="stats-card-icon bi {{ $icon }}"></i>
    </div>

    <div class="stats-card-value">{{ $value }}</div>
    <div class="stats-card-label">{{ $label }}</div>

    @if($change !== null)
    <div class="stats-card-change {{ $changeType }}">
        <i class="bi bi-arrow-{{ $changeType === 'positive' ? 'up' : ($changeType === 'negative' ? 'down' : 'right') }}"></i>
        <span>{{ $changeText }}</span>
    </div>
    @endif

    @if($trend)
    <div class="mt-3">
        <small class="text-muted">{{ $trend }}</small>
    </div>
    @endif
</div>
