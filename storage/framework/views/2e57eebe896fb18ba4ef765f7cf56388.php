<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --is-gold: #d4af37;
        --is-gold-dark: #aa7c11;
        --is-gold-light: #f3e5ab;
        --is-dark: #1d2127;
        --is-surface: #ffffff;
        --is-muted: #64748b;
        --is-border: #e8ecf1;
        --is-radius: 14px;
        --is-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
    }

    body, .content-body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .content-body { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); min-height: calc(100vh - 60px); }

    .is-page-header {
        background: linear-gradient(135deg, var(--is-dark) 0%, #2d3748 100%);
        border-radius: var(--is-radius);
        padding: 1.5rem 1.75rem;
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: var(--is-shadow);
        position: relative;
        overflow: hidden;
    }
    .is-page-header::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(212,175,55,0.25) 0%, transparent 70%);
    }
    .is-page-header h4, .is-page-header h5 { font-weight: 800; margin-bottom: .25rem; position: relative; z-index: 1; }
    .is-page-header p { color: rgba(255,255,255,.75); margin: 0; font-size: .9rem; position: relative; z-index: 1; }

    .is-card {
        background: var(--is-surface);
        border: 1px solid var(--is-border);
        border-radius: var(--is-radius);
        box-shadow: var(--is-shadow);
        overflow: hidden;
    }
    .is-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--is-border);
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .is-card-header h5, .is-card-header h6 { margin: 0; font-weight: 700; color: #1e293b; }
    .is-card-body { padding: 1.25rem; }

    .is-stat-card {
        border: 0;
        border-radius: var(--is-radius);
        box-shadow: var(--is-shadow);
        overflow: hidden;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .is-stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(15,23,42,.1); }
    .is-stat-card .card-body { padding: 1.25rem; position: relative; }
    .is-stat-card .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; margin-bottom: .75rem;
    }
    .is-stat-card .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .is-stat-card .stat-label { font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }

    .is-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 1px solid var(--is-border) !important;
        padding: .9rem 1rem !important;
        white-space: nowrap;
    }
    .is-table tbody td { padding: .85rem 1rem !important; vertical-align: middle; color: #334155; border-color: #f1f5f9 !important; }
    .is-table tbody tr:hover { background: #fafbfc; }

    .is-btn-gold {
        background: linear-gradient(135deg, var(--is-gold), var(--is-gold-dark)) !important;
        border: none !important;
        color: var(--is-dark) !important;
        font-weight: 700 !important;
        border-radius: 10px !important;
        padding: .5rem 1rem !important;
    }
    .is-btn-gold:hover { background: var(--is-gold-light) !important; color: var(--is-dark) !important; }

    .is-btn-soft { border-radius: 8px !important; font-weight: 600 !important; font-size: .8rem !important; }
    .is-btn-soft-primary { background: #eff6ff; color: #2563eb; border: none; }
    .is-btn-soft-danger { background: #fef2f2; color: #dc2626; border: none; }

    .is-badge {
        border-radius: 20px;
        padding: .35rem .75rem;
        font-weight: 600;
        font-size: .75rem;
        color: #ffffff !important;
        display: inline-block;
        line-height: 1.2;
    }
    .is-badge.bg-secondary {
        background-color: #64748b !important;
        color: #ffffff !important;
    }
    .is-badge.bg-success {
        background-color: #16a34a !important;
        color: #ffffff !important;
    }
    .is-badge.bg-warning {
        background-color: #ca8a04 !important;
        color: #ffffff !important;
    }
    .is-badge.bg-danger {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }
    .is-badge.bg-primary {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    .is-form .form-label { font-weight: 600; font-size: .85rem; color: #475569; }
    .is-form .form-control, .is-form .form-select {
        border-radius: 10px; border-color: var(--is-border); padding: .6rem .85rem;
    }
    .is-form .form-control:focus, .is-form .form-select:focus {
        border-color: var(--is-gold); box-shadow: 0 0 0 .2rem rgba(212,175,55,.15);
    }

    .is-breadcrumb-hint {
        display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
        padding: .75rem 1rem; background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 10px; font-size: .85rem; color: #92400e; margin-bottom: 1rem;
    }
    .is-breadcrumb-hint a { color: #b45309; font-weight: 600; text-decoration: none; }
    .is-breadcrumb-hint a:hover { text-decoration: underline; }

    .is-chart-card .card-header {
        background: #fff; border-bottom: 1px solid var(--is-border);
        font-weight: 700; color: #1e293b;
    }
    .is-empty { text-align: center; padding: 2.5rem 1rem; color: var(--is-muted); }
</style>
<?php /**PATH C:\laragon\www\iron-smart\resources\views/partials/ui-styles.blade.php ENDPATH**/ ?>