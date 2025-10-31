{{-- مكون الفلاتر --}}
@props([
    'filters' => [],
    'searchPlaceholder' => 'البحث...',
    'searchValue' => '',
    'actionUrl' => ''
])

<div class="filters-container">
    <form method="GET" action="{{ $actionUrl ?: request()->url() }}">
        <div class="filters-grid">
            {{-- البحث --}}
            <div class="filter-group">
                <label class="filter-label">البحث</label>
                <input type="text"
                       class="filter-input"
                       name="search"
                       placeholder="{{ $searchPlaceholder }}"
                       value="{{ $searchValue ?: request('search') }}">
            </div>

            {{-- الفلاتر المخصصة --}}
            @foreach($filters as $filter)
            <div class="filter-group">
                @if(isset($filter['label']))
                <label class="filter-label">{{ $filter['label'] }}</label>
                @endif

                @if(isset($filter['type']) && $filter['type'] === 'select')
                <select class="filter-input" name="{{ $filter['name'] }}">
                    <option value="">{{ $filter['placeholder'] ?? 'اختر...' }}</option>
                    @foreach($filter['options'] as $value => $text)
                    <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                        {{ $text }}
                    </option>
                    @endforeach
                </select>
                @elseif(isset($filter['type']) && $filter['type'] === 'date')
                <input type="date"
                       class="filter-input"
                       name="{{ $filter['name'] }}"
                       value="{{ request($filter['name']) }}">
                @elseif(isset($filter['type']) && $filter['type'] === 'daterange')
                <div class="d-flex gap-2">
                    <input type="date"
                           class="filter-input"
                           name="{{ $filter['name'] }}_from"
                           placeholder="من"
                           value="{{ request($filter['name'] . '_from') }}">
                    <input type="date"
                           class="filter-input"
                           name="{{ $filter['name'] }}_to"
                           placeholder="إلى"
                           value="{{ request($filter['name'] . '_to') }}">
                </div>
                @else
                <input type="{{ $filter['type'] ?? 'text' }}"
                       class="filter-input"
                       name="{{ $filter['name'] }}"
                       placeholder="{{ $filter['placeholder'] ?? '' }}"
                       value="{{ request($filter['name']) }}">
                @endif
            </div>
            @endforeach

            {{-- أزرار الإجراءات --}}
            <div class="filter-group d-flex align-items-end gap-2">
                <button type="submit" class="filter-btn flex-fill">
                    <i class="bi bi-search"></i>
                    بحث
                </button>
                <a href="{{ $actionUrl ?: request()->url() }}" class="btn btn-outline-secondary flex-fill">
                    <i class="bi bi-arrow-clockwise"></i>
                    إعادة تعيين
                </a>
            </div>
        </div>
    </form>
</div>
