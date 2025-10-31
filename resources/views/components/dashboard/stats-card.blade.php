{{-- مكون بطاقة الإحصائيات --}}
@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'change' => null,
    'changeType' => 'neutral', // positive, negative, neutral
    'changeText' => '',
    'color' => 'primary'
])

<div class="card stats-card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="stats-number">{{ $value }}</div>
                <div class="stats-label">{{ $title }}</div>
                @if($change !== null)
                <div class="mt-3">
                    <span class="badge badge-{{ $changeType === 'positive' ? 'success' : ($changeType === 'negative' ? 'danger' : 'info') }}">
                        <i class="bi bi-arrow-{{ $changeType === 'positive' ? 'up' : ($changeType === 'negative' ? 'down' : 'right') }} me-1"></i>
                        {{ $changeText }}
                    </span>
                </div>
                @endif
            </div>
            <div class="text-primary">
                <i class="bi {{ $icon }} fs-1"></i>
            </div>
        </div>
    </div>
</div>
