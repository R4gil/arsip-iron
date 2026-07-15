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

    /* Modern Button Styles */
    .btn {
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        padding: 0.6rem 1.2rem !important;
        transition: all 0.3s ease !important;
        border: 1.5px solid transparent !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }
    .btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .btn:active {
        transform: translateY(0) !important;
    }

    /* Primary Button - Blue Gradient */
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #60a5fa, #3b82f6) !important;
        color: #ffffff !important;
    }

    /* Success Button - Green Gradient */
    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-success:hover {
        background: linear-gradient(135deg, #4ade80, #22c55e) !important;
        color: #ffffff !important;
    }

    /* Danger Button - Red Gradient */
    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #f87171, #ef4444) !important;
        color: #ffffff !important;
    }

    /* Warning Button - Amber Gradient */
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-warning:hover {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #1e293b !important;
    }

    /* Info Button - Cyan Gradient */
    .btn-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-info:hover {
        background: linear-gradient(135deg, #22d3ee, #06b6d4) !important;
        color: #ffffff !important;
    }

    /* Secondary Button - Gray */
    .btn-secondary {
        background: linear-gradient(135deg, #64748b, #475569) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-secondary:hover {
        background: linear-gradient(135deg, #94a3b8, #64748b) !important;
        color: #ffffff !important;
    }

    /* Light Button */
    .btn-light {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
        color: #334155 !important;
        border: 1.5px solid #e2e8f0 !important;
    }
    .btn-light:hover {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }

    /* Outline Buttons */
    .btn-outline-primary {
        background: transparent !important;
        color: #3b82f6 !important;
        border: 2px solid #3b82f6 !important;
    }
    .btn-outline-primary:hover {
        background: #3b82f6 !important;
        color: #ffffff !important;
    }

    .btn-outline-success {
        background: transparent !important;
        color: #22c55e !important;
        border: 2px solid #22c55e !important;
    }
    .btn-outline-success:hover {
        background: #22c55e !important;
        color: #ffffff !important;
    }

    .btn-outline-danger {
        background: transparent !important;
        color: #ef4444 !important;
        border: 2px solid #ef4444 !important;
    }
    .btn-outline-danger:hover {
        background: #ef4444 !important;
        color: #ffffff !important;
    }

    .btn-outline-warning {
        background: transparent !important;
        color: #f59e0b !important;
        border: 2px solid #f59e0b !important;
    }
    .btn-outline-warning:hover {
        background: #f59e0b !important;
        color: #ffffff !important;
    }

    .btn-outline-info {
        background: transparent !important;
        color: #06b6d4 !important;
        border: 2px solid #06b6d4 !important;
    }
    .btn-outline-info:hover {
        background: #06b6d4 !important;
        color: #ffffff !important;
    }

    /* Gold Button - Special */
    .is-btn-gold {
        background: linear-gradient(135deg, var(--is-gold), var(--is-gold-dark)) !important;
        border: none !important;
        color: var(--is-dark) !important;
        font-weight: 700 !important;
        border-radius: 10px !important;
        padding: .6rem 1.2rem !important;
        box-shadow: 0 3px 10px rgba(212,175,55,0.3) !important;
    }
    .is-btn-gold:hover { 
        background: linear-gradient(135deg, #fbbf24, var(--is-gold)) !important; 
        color: var(--is-dark) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(212,175,55,0.4) !important;
    }

    /* Soft Button Styles */
    .is-btn-soft { 
        border-radius: 8px !important; 
        font-weight: 600 !important; 
        font-size: .8rem !important; 
        padding: 0.5rem 1rem !important;
    }
    .is-btn-soft-primary { 
        background: #eff6ff !important; 
        color: #2563eb !important; 
        border: 1.5px solid #dbeafe !important;
    }
    .is-btn-soft-primary:hover {
        background: #dbeafe !important;
    }
    .is-btn-soft-danger { 
        background: #fef2f2 !important; 
        color: #dc2626 !important; 
        border: 1.5px solid #fee2e2 !important;
    }
    .is-btn-soft-danger:hover {
        background: #fee2e2 !important;
    }
    .is-btn-soft-success {
        background: #f0fdf4 !important;
        color: #16a34a !important;
        border: 1.5px solid #dcfce7 !important;
    }
    .is-btn-soft-success:hover {
        background: #dcfce7 !important;
    }
    .is-btn-soft-warning {
        background: #fefce8 !important;
        color: #ca8a04 !important;
        border: 1.5px solid #fef9c3 !important;
    }
    .is-btn-soft-warning:hover {
        background: #fef9c3 !important;
    }

    /* Button Sizes */
    .btn-sm {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.75rem !important;
    }
    .btn-lg {
        padding: 0.8rem 1.6rem !important;
        font-size: 0.95rem !important;
    }

    /* Button Groups */
    .btn-group .btn {
        margin: 0 !important;
        border-radius: 0 !important;
    }
    .btn-group .btn:first-child {
        border-radius: 10px 0 0 10px !important;
    }
    .btn-group .btn:last-child {
        border-radius: 0 10px 10px 0 !important;
    }

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
