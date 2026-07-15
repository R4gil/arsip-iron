@php
    $notifikasiList = $notifikasiList ?? collect();
    $notifikasiCount = $notifikasiCount ?? 0;
@endphp

<div id="notifikasi-box" class="notifikasi-box me-2">
    <a href="#" class="notifikasi-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
        <i class="fas fa-bell"></i>
        @if($notifikasiCount > 0)
            <span class="notifikasi-badge" id="notifikasi-badge">{{ $notifikasiCount > 9 ? '9+' : $notifikasiCount }}</span>
        @else
            <span class="notifikasi-badge d-none" id="notifikasi-badge">0</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end notifikasi-dropdown shadow-sm">
        <div class="notifikasi-dropdown-header">
            <span class="fw-bold">Notifikasi</span>
            @if($notifikasiCount > 0)
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="notifikasi-dismiss-all">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="notifikasi-list" id="notifikasi-list">
            @forelse($notifikasiList as $notif)
                <a href="{{ $notif['url'] }}"
                   class="notifikasi-item"
                   data-key="{{ $notif['key'] }}">
                    <span class="notifikasi-item-icon" style="background: {{ $notif['warna'] }}15; color: {{ $notif['warna'] }};">
                        <i class="fas {{ $notif['ikon'] }}"></i>
                    </span>
                    <span class="notifikasi-item-body">
                        <span class="notifikasi-item-title">{{ $notif['judul'] }}</span>
                        <span class="notifikasi-item-message">{{ $notif['pesan'] }}</span>
                        <span class="notifikasi-item-time">{{ $notif['waktu_label'] }}</span>
                    </span>
                </a>
            @empty
                <div class="notifikasi-empty" id="notifikasi-empty">
                    <i class="fas fa-check-circle text-success mb-2"></i>
                    <p class="mb-0">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .notifikasi-box { position: relative; display: inline-flex; align-items: center; }
    .notifikasi-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .notifikasi-toggle:hover { background: #f1f5f9; color: #d4af37; }
    .notifikasi-toggle .fa-bell { font-size: 1.15rem; }
    .notifikasi-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #dc2626;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
    }
    .notifikasi-dropdown {
        width: 360px;
        max-width: 90vw;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0;
        overflow: hidden;
    }
    .notifikasi-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f7;
        background: #fafafa;
        font-size: 0.9rem;
    }
    .notifikasi-list { max-height: 380px; overflow-y: auto; }
    .notifikasi-item {
        display: flex;
        gap: 12px;
        padding: 12px 16px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .notifikasi-item:hover { background: #f8fafc; }
    .notifikasi-item.is-dismissing { opacity: 0; transform: translateX(8px); transition: all 0.2s ease; }
    .notifikasi-item-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .notifikasi-item-body { display: flex; flex-direction: column; min-width: 0; }
    .notifikasi-item-title { font-weight: 700; font-size: 0.85rem; color: #1e293b; }
    .notifikasi-item-message { font-size: 0.8rem; color: #64748b; line-height: 1.35; }
    .notifikasi-item-time { font-size: 0.72rem; color: #94a3b8; margin-top: 2px; }
    .notifikasi-empty {
        padding: 28px 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('#logout-form input[name="_token"]')?.value;

    function updateBadge(count) {
        const badge = document.getElementById('notifikasi-badge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function showEmptyState() {
        const list = document.getElementById('notifikasi-list');
        if (!list || list.querySelector('.notifikasi-item')) return;
        list.innerHTML = '<div class="notifikasi-empty" id="notifikasi-empty"><i class="fas fa-check-circle text-success mb-2"></i><p class="mb-0">Tidak ada notifikasi baru</p></div>';
        const dismissAll = document.getElementById('notifikasi-dismiss-all');
        if (dismissAll) dismissAll.remove();
    }

    async function dismissNotification(key, element) {
        if (!key) return;
        try {
            const response = await fetch('{{ route('notifikasi.dismiss') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ key }),
            });
            const data = await response.json();
            if (element) {
                element.classList.add('is-dismissing');
                setTimeout(() => {
                    element.remove();
                    updateBadge(data.count ?? 0);
                    showEmptyState();
                }, 180);
            } else {
                updateBadge(data.count ?? 0);
            }
        } catch (e) {
            console.error('Gagal menutup notifikasi', e);
        }
    }

    document.querySelectorAll('.notifikasi-item').forEach(function (item) {
        item.addEventListener('click', function (e) {
            const key = this.dataset.key;
            dismissNotification(key, this);
        });
    });

    const dismissAllBtn = document.getElementById('notifikasi-dismiss-all');
    if (dismissAllBtn) {
        dismissAllBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            const keys = Array.from(document.querySelectorAll('.notifikasi-item')).map(el => el.dataset.key);
            try {
                await fetch('{{ route('notifikasi.dismissAll') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ keys }),
                });
                document.querySelectorAll('.notifikasi-item').forEach(el => el.remove());
                updateBadge(0);
                showEmptyState();
            } catch (err) {
                console.error('Gagal menandai semua notifikasi', err);
            }
        });
    }
});
</script>
