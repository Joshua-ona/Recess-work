<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduDiscuss') — E-Discussion Platform</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.4.0/tabler-icons.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple-50:  #EEEDFE; --purple-200: #AFA9EC; --purple-400: #7F77DD;
            --purple-600: #534AB7; --purple-800: #3C3489; --purple-900: #26215C;
            --teal-50:  #E1F5EE; --teal-400: #1D9E75; --teal-600: #0F6E56; --teal-800: #085041;
            --amber-50: #FAEEDA; --amber-400: #BA7517; --amber-600: #854F0B; --amber-800: #633806;
            --red-50:   #FCEBEB; --red-400: #E24B4A; --red-600: #A32D2D;
            --blue-50:  #E6F1FB; --blue-400: #378ADD; --blue-600: #185FA5; --blue-800: #0C447C;
            --green-50: #EAF3DE; --green-600: #3B6D11;
            --pink-50:  #FBEAF0; --pink-600: #993556; --pink-800: #72243E;
            --gray-100: #D3D1C7; --gray-400: #888780; --gray-600: #5F5E5A;
            --radius-sm: 6px; --radius-md: 8px; --radius-lg: 12px; --radius-xl: 16px;
            --border: 0.5px solid rgba(0,0,0,0.12);
            --border-em: 0.5px solid rgba(0,0,0,0.22);
            --bg: #F5F4F0; --surface: #fff; --text: #1a1a1a; --muted: #6b6a66; --hint: #9b9a96;
            --focus-ring: 0 0 0 3px var(--purple-50);
        }

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: var(--bg); color: var(--text); font-size: 15px; line-height: 1.6; }

        /* ── Auth layout ── */
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center;
                     padding: 2rem; background: linear-gradient(135deg, var(--purple-50) 0%, var(--teal-50) 100%); }
        .auth-card { background: var(--surface); border-radius: var(--radius-xl);
                     border: var(--border); padding: 2.5rem; width: 100%; max-width: 430px; }
        .auth-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; }
        .auth-logo-icon { width: 38px; height: 38px; background: var(--purple-800); border-radius: var(--radius-md);
                          display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px; }
        .auth-logo-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .auth-logo-sub  { font-size: 11px; color: var(--muted); }
        .auth-heading   { font-size: 22px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
        .auth-sub       { font-size: 14px; color: var(--muted); margin-bottom: 1.75rem; }

        /* ── Forms ── */
        .form-group  { margin-bottom: 1rem; }
        .form-label  { display: block; font-size: 13px; color: var(--muted); margin-bottom: 5px; }
        .form-row    { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .input-wrap  { position: relative; }
        .input-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
                        color: var(--hint); font-size: 16px; pointer-events: none; }
        .input-wrap input { padding-left: 36px; }
        .form-control { width: 100%; padding: 9px 12px; border: var(--border-em);
                        border-radius: var(--radius-md); font-size: 14px; background: var(--surface);
                        color: var(--text); font-family: inherit; transition: border-color .15s, box-shadow .15s; }
        .form-control:focus { outline: none; border-color: var(--purple-600); box-shadow: var(--focus-ring); }
        .form-control.is-invalid { border-color: var(--red-400); }
        .invalid-feedback { font-size: 12px; color: var(--red-600); margin-top: 4px; display: block; }
        .form-check  { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--muted); }
        .form-check input[type="checkbox"] { width: 14px; height: 14px; accent-color: var(--purple-600); cursor: pointer; }
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .link-sm { font-size: 13px; color: var(--purple-600); text-decoration: none; }
        .link-sm:hover { text-decoration: underline; }
        .auth-footer { text-align: center; margin-top: 1.25rem; font-size: 13px; color: var(--muted); }
        .auth-footer a { color: var(--purple-600); text-decoration: none; }

        /* Role selector */
        .role-selector { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 1.25rem; }
        .role-btn { padding: 10px 8px; border: var(--border-em); border-radius: var(--radius-md);
                    background: transparent; cursor: pointer; text-align: center;
                    font-size: 12px; color: var(--muted); font-family: inherit; transition: all .15s; }
        .role-btn i { display: block; font-size: 22px; margin-bottom: 5px; }
        .role-btn:hover, .role-btn.selected {
            border-color: var(--purple-600); background: var(--purple-50); color: var(--purple-800); }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
               border-radius: var(--radius-md); font-size: 14px; font-weight: 500;
               font-family: inherit; cursor: pointer; border: var(--border-em); transition: all .15s; }
        .btn-primary { background: var(--purple-800); color: #fff; border-color: var(--purple-800); width: 100%; justify-content: center; }
        .btn-primary:hover { background: var(--purple-600); border-color: var(--purple-600); }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-outline { background: transparent; color: var(--text); }
        .btn-outline:hover { background: var(--bg); }
        .btn-danger-sm { padding: 4px 10px; font-size: 12px; background: var(--red-50);
                         color: var(--red-600); border-color: transparent; border-radius: var(--radius-sm); }

        /* ── Dashboard shell ── */
        .dash-wrap  { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { background: var(--purple-900); display: flex; flex-direction: column; }
        .sidebar-logo { padding: 1.25rem; border-bottom: 0.5px solid rgba(255,255,255,0.08); }
        .sidebar-logo-row { display: flex; align-items: center; gap: 8px; }
        .sidebar-logo-icon { width: 30px; height: 30px; background: var(--purple-600);
                             border-radius: var(--radius-sm); display: flex; align-items: center;
                             justify-content: center; color: #fff; font-size: 15px; flex-shrink: 0; }
        .sidebar-logo-name { font-size: 14px; font-weight: 600; color: #fff; }
        .sidebar-logo-sub  { font-size: 11px; color: rgba(255,255,255,0.45); }
        .sidebar-section { padding: 0 0.75rem; margin-top: 0.75rem; }
        .sidebar-section-label { font-size: 10px; color: rgba(255,255,255,0.38);
                                 text-transform: uppercase; letter-spacing: 0.9px;
                                 padding: 0 8px; margin-bottom: 4px; }
        .sidebar-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px;
                        border-radius: var(--radius-md); color: rgba(255,255,255,0.62);
                        font-size: 13px; cursor: pointer; margin-bottom: 1px; text-decoration: none;
                        transition: background .15s, color .15s; }
        .sidebar-item:hover, .sidebar-item.active { background: rgba(83,74,183,.45); color: #fff; }
        .sidebar-item i { font-size: 16px; flex-shrink: 0; }
        .sidebar-badge { margin-left: auto; background: var(--purple-600); color: #fff;
                         font-size: 10px; padding: 1px 6px; border-radius: 10px; font-weight: 600; }
        .sidebar-spacer { flex: 1; }
        .sidebar-user { padding: 1rem 1.25rem; border-top: 0.5px solid rgba(255,255,255,0.08);
                        display: flex; align-items: center; gap: 10px; }
        .sidebar-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--purple-600);
                          display: flex; align-items: center; justify-content: center;
                          color: #fff; font-size: 12px; font-weight: 600; flex-shrink: 0; }
        .sidebar-user-name { font-size: 12px; font-weight: 500; color: #fff; }
        .sidebar-user-meta { font-size: 11px; color: rgba(255,255,255,0.42); }

        /* ── Main area ── */
        .dash-main { display: flex; flex-direction: column; background: var(--bg); }
        .dash-header { background: var(--surface); border-bottom: var(--border); padding: 14px 1.5rem;
                       display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .dash-header-title { font-size: 15px; font-weight: 600; color: var(--text); }
        .dash-header-sub   { font-size: 12px; color: var(--muted); }
        .dash-header-actions { display: flex; align-items: center; gap: 8px; }
        .icon-btn { width: 34px; height: 34px; border: var(--border-em); border-radius: var(--radius-md);
                    display: flex; align-items: center; justify-content: center; cursor: pointer;
                    background: transparent; color: var(--muted); transition: background .15s; }
        .icon-btn:hover { background: var(--bg); }
        .dash-body { padding: 1.5rem; flex: 1; overflow-y: auto; }

        /* ── Stat cards ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 1.5rem; }
        .stat-card { background: var(--surface); border: var(--border); border-radius: var(--radius-lg); padding: 1rem; }
        .stat-label { font-size: 12px; color: var(--muted); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .stat-label i { font-size: 15px; }
        .stat-value { font-size: 26px; font-weight: 600; color: var(--text); line-height: 1.1; }
        .stat-change { font-size: 12px; margin-top: 4px; }
        .text-pos { color: var(--teal-600); } .text-neg { color: var(--red-600); } .text-muted { color: var(--hint); }

        /* ── Panels ── */
        .panel-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
        .panel { background: var(--surface); border: var(--border); border-radius: var(--radius-lg); }
        .panel-head { padding: 14px 1rem; border-bottom: var(--border);
                      display: flex; align-items: center; justify-content: space-between; }
        .panel-title  { font-size: 13px; font-weight: 600; color: var(--text); }
        .panel-action { font-size: 12px; color: var(--purple-600); cursor: pointer; text-decoration: none; }
        .panel-body   { padding: 1rem; }
        .full-panel   { background: var(--surface); border: var(--border); border-radius: var(--radius-lg); }

        /* ── Discussion rows ── */
        .disc-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 0;
                     border-bottom: var(--border); }
        .disc-item:last-child { border-bottom: none; padding-bottom: 0; }
        .disc-avatar { width: 30px; height: 30px; border-radius: 50%; display: flex;
                       align-items: center; justify-content: center; font-size: 11px;
                       font-weight: 600; flex-shrink: 0; }
        .disc-title { font-size: 13px; font-weight: 500; color: var(--text);
                      white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .disc-meta  { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* ── Activity feed ── */
        .activity-item { display: flex; gap: 10px; padding: 8px 0; border-bottom: var(--border); }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot  { width: 8px; height: 8px; border-radius: 50%; background: var(--purple-600); margin-top: 5px; flex-shrink: 0; }
        .activity-text { font-size: 13px; color: var(--text); }
        .activity-time { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* ── Table ── */
        .table-scroll { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { text-align: left; padding: 10px 14px; color: var(--muted); font-weight: 500;
                         border-bottom: var(--border); white-space: nowrap; }
        .data-table td { padding: 11px 14px; border-bottom: var(--border); color: var(--text); vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: var(--bg); }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; font-size: 11px; padding: 2px 8px;
                 border-radius: 10px; font-weight: 600; }
        .badge-green  { background: var(--green-50);  color: var(--green-600); }
        .badge-amber  { background: var(--amber-50);  color: var(--amber-600); }
        .badge-blue   { background: var(--blue-50);   color: var(--blue-600); }
        .badge-red    { background: var(--red-50);    color: var(--red-600); }
        .badge-purple { background: var(--purple-50); color: var(--purple-800); }
        .badge-teal   { background: var(--teal-50);   color: var(--teal-600); }

        /* ── Progress bar ── */
        .progress-wrap  { margin-bottom: 14px; }
        .progress-label { display: flex; justify-content: space-between; font-size: 12px;
                          color: var(--muted); margin-bottom: 4px; }
        .progress-label span:last-child { font-weight: 600; color: var(--text); }
        .progress-bar   { height: 5px; background: var(--bg); border-radius: 3px; overflow: hidden; }
        .progress-fill  { height: 100%; border-radius: 3px; transition: width .4s; }

        /* ── Alerts ── */
        .alert { padding: 10px 14px; border-radius: var(--radius-md); font-size: 13px; margin-bottom: 1rem; border: var(--border); }
        .alert-danger  { background: var(--red-50);  color: var(--red-600);  border-color: rgba(226,75,74,.2); }
        .alert-success { background: var(--green-50); color: var(--green-600); border-color: rgba(99,153,34,.2); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .dash-wrap  { grid-template-columns: 1fr; }
            .sidebar    { display: none; }
            .panel-grid { grid-template-columns: 1fr; }
            .form-row   { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('body')
    @stack('scripts')
</body>
</html>