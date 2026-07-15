@props(['title', 'subtitle' => null, 'action' => null, 'actionLabel' => null, 'actionIcon' => 'fa-plus', 'extraActions' => null])

<div class="is-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-1">{{ $title }}</h4>
        @if($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($extraActions)
            @foreach($extraActions as $extraAction)
                <a href="{{ $extraAction['url'] }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas {{ $extraAction['icon'] }} me-1"></i>{{ $extraAction['label'] }}
                </a>
            @endforeach
        @endif
        @if($action)
            <a href="{{ $action }}" class="btn is-btn-gold">
                <i class="fas {{ $actionIcon }} me-2"></i>{{ $actionLabel ?? 'Tambah' }}
            </a>
        @endif
    </div>
</div>
