{{-- مكون الجدول --}}
@props([
    'title' => '',
    'icon' => '',
    'actions' => [],
    'headers' => [],
    'data' => [],
    'emptyMessage' => 'لا توجد بيانات للعرض'
])

<div class="table-container">
    <div class="table-header">
        <h5 class="table-title">
            <i class="bi {{ $icon }}"></i>
            {{ $title }}
        </h5>

        @if(!empty($actions))
        <div class="table-actions">
            @foreach($actions as $action)
            <a href="{{ $action['url'] ?? '#' }}" class="table-action-btn">
                <i class="bi {{ $action['icon'] ?? '' }}"></i>
                {{ $action['text'] ?? '' }}
            </a>
            @endforeach
        </div>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    @foreach($headers as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        {{ $emptyMessage }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
