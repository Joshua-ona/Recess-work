<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduDiscuss') — E-Discussion Platform</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="{{ asset('css/groups.css') }}">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @stack('group-styles')
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    @livewireStyles
    
    <style>
    /* ============================================================
       YOUR EXISTING STYLES (KEEP EVERYTHING AS IS)
       ============================================================ */
    *::before,

    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }


    :root {
        --purple-50: #EEEDFE;
        --purple-200: #AFA9EC;
        --purple-400: #7F77DD;
        --purple-600: #534AB7;
        --purple-800: #3C3489;
        --purple-900: #26215C;
        --teal-50: #E1F5EE;
        --teal-400: #1D9E75;
        --teal-600: #0F6E56;
        --teal-800: #085041;
        --amber-50: #FAEEDA;
        --amber-400: #BA7517;
        --amber-600: #854F0B;
        --amber-800: #633806;
        --red-50: #FCEBEB;
        --red-400: #E24B4A;
        --red-600: #A32D2D;
        --blue-50: #E6F1FB;
        --blue-400: #378ADD;
        --blue-600: #185FA5;
        --blue-800: #0C447C;
        --green-50: #EAF3DE;
        --green-600: #3B6D11;
        --pink-50: #FBEAF0;
        --pink-600: #993556;
        --pink-800: #72243E;
        --gray-100: #D3D1C7;
        --gray-400: #888780;
        --gray-600: #5F5E5A;
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --border: 0.5px solid rgba(0, 0, 0, 0.12);
        --border-em: 0.5px solid rgba(0, 0, 0, 0.22);
        --bg: #F5F4F0;
        --surface: #fff;
        --text: #1a1a1a;
        --muted: #6b6a66;
        --hint: #9b9a96;
        --focus-ring: 0 0 0 3px var(--purple-50);
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg);
        color: var(--text);
        font-size: 15px;
        line-height: 1.6;
    }

    /* ── Auth layout ── */
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: linear-gradient(135deg, var(--purple-50) 0%, var(--teal-50) 100%);
    }

    .auth-card {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: var(--border);
        padding: 2.5rem;
        width: 100%;
        max-width: 430px;
    }

    .auth-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 2rem;
    }

    .auth-logo-icon {
        width: 38px;
        height: 38px;
        background: var(--purple-800);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
    }

    .auth-logo-name {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
    }

    .auth-logo-sub {
        font-size: 11px;
        color: var(--muted);
    }

    .auth-heading {
        font-size: 22px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .auth-sub {
        font-size: 14px;
        color: var(--muted);
        margin-bottom: 1.75rem;
    }

    /* ── Forms ── */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .input-wrap {
        position: relative;
    }

    .input-wrap i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--hint);
        font-size: 16px;
        pointer-events: none;
    }

    .input-wrap input {
        padding-left: 36px;
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: var(--border-em);
        border-radius: var(--radius-md);
        font-size: 14px;
        background: var(--surface);
        color: var(--text);
        font-family: inherit;
        transition: border-color .15s, box-shadow .15s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--purple-600);
        box-shadow: var(--focus-ring);
    }

    .form-control.is-invalid {
        border-color: var(--red-400);
    }

    .invalid-feedback {
        font-size: 12px;
        color: var(--red-600);
        margin-top: 4px;
        display: block;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--muted);
    }

    .form-check input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: var(--purple-600);
        cursor: pointer;
    }

    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .link-sm {
        font-size: 13px;
        color: var(--purple-600);
        text-decoration: none;
    }

    .link-sm:hover {
        text-decoration: underline;
    }

    .auth-footer {
        text-align: center;
        margin-top: 1.25rem;
        font-size: 13px;
        color: var(--muted);
    }

    .auth-footer a {
        color: var(--purple-600);
        text-decoration: none;
    }

    /* Role selector */
    .role-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 1.25rem;
    }

    .role-btn {
        padding: 10px 8px;
        border: var(--border-em);
        border-radius: var(--radius-md);
        background: transparent;
        cursor: pointer;
        text-align: center;
        font-size: 12px;
        color: var(--muted);
        font-family: inherit;
        transition: all .15s;
    }

    .role-btn i {
        display: block;
        font-size: 22px;
        margin-bottom: 5px;
    }

    .role-btn:hover,
    .role-btn.selected {
        border-color: var(--purple-600);
        background: var(--purple-50);
        color: var(--purple-800);
    }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 500;
        font-family: inherit;
        cursor: pointer;
        border: var(--border-em);
        transition: all .15s;
    }

    .btn-primary {
        background: var(--purple-800);
        color: #fff;
        border-color: var(--purple-800);
        width: 100%;
        justify-content: center;
    }

    .btn-primary:hover {
        background: var(--purple-600);
        border-color: var(--purple-600);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-outline {
        background: transparent;
        color: var(--text);
    }

    .btn-outline:hover {
        background: var(--bg);
    }

    .btn-danger-sm {
        padding: 4px 10px;
        font-size: 12px;
        background: var(--red-50);
        color: var(--red-600);
        border-color: transparent;
        border-radius: var(--radius-sm);
    }

    /* ── Dashboard shell ── */
    .dash-wrap {
        display: grid;
        grid-template-columns: 220px 1fr;
        min-height: 100vh;
    }

    /* ── Sidebar ── */
    .sidebar {
        background: var(--purple-900);
        display: flex;
        flex-direction: column;
    }

    .sidebar-logo {
        padding: 1.25rem;
        border-bottom: 0.5px solid rgba(255, 255, 255, 0.08);
    }

    .sidebar-logo-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-logo-icon {
        width: 30px;
        height: 30px;
        background: var(--purple-600);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 15px;
        flex-shrink: 0;
    }

    .sidebar-logo-name {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }

    .sidebar-logo-sub {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.45);
    }

    .sidebar-section {
        padding: 0 0.75rem;
        margin-top: 0.75rem;
    }

    .sidebar-section-label {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.38);
        text-transform: uppercase;
        letter-spacing: 0.9px;
        padding: 0 8px;
        margin-bottom: 4px;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: var(--radius-md);
        color: rgba(255, 255, 255, 0.62);
        font-size: 13px;
        cursor: pointer;
        margin-bottom: 1px;
        text-decoration: none;
        transition: background .15s, color .15s;
    }

    .sidebar-item:hover,
    .sidebar-item.active {
        background: rgba(83, 74, 183, .45);
        color: #fff;
    }

    .sidebar-item i {
        font-size: 16px;
        flex-shrink: 0;
    }

    .sidebar-badge {
        margin-left: auto;
        background: var(--purple-600);
        color: #fff;
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 10px;
        font-weight: 600;
    }

    .sidebar-spacer {
        flex: 1;
    }

    .sidebar-user {
        padding: 1rem 1.25rem;
        border-top: 0.5px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--purple-600);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .sidebar-user-name {
        font-size: 12px;
        font-weight: 500;
        color: #fff;
    }

    .sidebar-user-meta {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.42);
    }

    /* ── Main area ── */
    .dash-main {
        display: flex;
        flex-direction: column;
        background: var(--bg);
    }

    .dash-header {
        background: var(--surface);
        border-bottom: var(--border);
        padding: 14px 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .dash-header-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
    }

    .dash-header-sub {
        font-size: 12px;
        color: var(--muted);
    }

    .dash-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .icon-btn {
        width: 34px;
        height: 34px;
        border: var(--border-em);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
        color: var(--muted);
        transition: background .15s;
    }

    .icon-btn:hover {
        background: var(--bg);
    }

   .dash-body {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0; /* your existing margins on stat-grid/panel-grid already add spacing */
}

    /* ── Stat cards ── */
.stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 1.5rem;
        overflow: visible;
    }

    .stat-card {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-lg);
        padding: 1rem;
        transition: box-shadow .15s, transform .15s;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
    }

    .stat-label {
        flex-wrap: wrap;
        width: 100%;
    }

    .stat-value {
        white-space: nowrap;
        width: 100%;
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .stat-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
        transform: translateY(-1px);
    }

    .stat-label {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-label i {
        font-size: 15px;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.1;
    }

    .stat-change {
        font-size: 12px;
        margin-top: 4px;
    }

    .text-pos {
        color: var(--teal-600);
    }

    .text-neg {
        color: var(--red-600);
    }

    .text-muted {
        color: var(--hint);
    }

    /* ── Panels ── */
    .panel-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-lg);
        transition: box-shadow .15s;
    }

    .panel:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
    }

    .panel-head {
        padding: 14px 1rem;
        border-bottom: var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }

    .panel-action {
        font-size: 12px;
        color: var(--purple-600);
        cursor: pointer;
        text-decoration: none;
    }

    .panel-body {
        padding: 1rem;
    }

    .full-panel {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-lg);
    }

    /* ── Discussion rows ── */
    .disc-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: var(--border);
    }

    .disc-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .disc-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .disc-title {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .disc-meta {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ── Activity feed ── */
    .activity-item {
        display: flex;
        gap: 10px;
        padding: 8px 0;
        border-bottom: var(--border);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--purple-600);
        margin-top: 5px;
        flex-shrink: 0;
    }

    .activity-text {
        font-size: 13px;
        color: var(--text);
    }

    .activity-time {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }
    .notification-card{
        padding: 15px;
        margin-top: 15px;
        margin-bottom: 5px;
        border: 2px, solid grey;
        border-radius: 9px;
        box-shadow: 3px 2px 10px black;
    }

    /* ── Table ── */
    .table-scroll {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .data-table th {
        text-align: left;
        padding: 10px 14px;
        color: var(--muted);
        font-weight: 500;
        border-bottom: var(--border);
        white-space: nowrap;
    }

    .data-table td {
        padding: 11px 14px;
        border-bottom: var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover td {
        background: var(--bg);
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .badge-green {
        background: var(--green-50);
        color: var(--green-600);
    }

    .badge-amber {
        background: var(--amber-50);
        color: var(--amber-600);
    }

    .badge-blue {
        background: var(--blue-50);
        color: var(--blue-600);
    }

    .badge-red {
        background: var(--red-50);
        color: var(--red-600);
    }

    .badge-purple {
        background: var(--purple-50);
        color: var(--purple-800);
    }

    .badge-teal {
        background: var(--teal-50);
        color: var(--teal-600);
    }

    /* ── Progress bar ── */
    .progress-wrap {
        margin-bottom: 14px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .progress-label span:last-child {
        font-weight: 600;
        color: var(--text);
    }

    .progress-bar {
        height: 5px;
        background: var(--bg);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width .4s;
    }

    /* ── Alerts ── */
    .alert {
        padding: 10px 14px;
        border-radius: var(--radius-md);
        font-size: 13px;
        margin-bottom: 1rem;
        border: var(--border);
    }

    .alert-danger {
        background: var(--red-50);
        color: var(--red-600);
        border-color: rgba(226, 75, 74, .2);
    }

    .alert-success {
        background: var(--green-50);
        color: var(--green-600);
        border-color: rgba(99, 153, 34, .2);
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .dash-wrap {
            grid-template-columns: 1fr;
        }

        .sidebar {
            display: none;
        }

        .panel-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    /* ================= CHAT ================= */

        .quiz-nav-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    

    /* ================= CHAT ================= */

   .chat-wrapper{
    display:flex;
    height:75vh;
    width:100%;
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    border:var(--border);
}

    .chat-users {
        width: 320px;
        background: #fff;
        border-right: var(--border);
        display: flex;
        flex-direction: column;
    }

    .chat-users-header {
        padding: 18px;
        font-weight: 600;
        font-size: 18px;
        border-bottom: var(--border);
    }

    .chat-search {
        width: 100%;
        padding: 10px 14px;
        border: var(--border);
        border-radius: 999px;
        outline: none;
        font-size: 14px;
        margin-top: 4px;
    }

    .chat-users-list {
        overflow-y: auto;
        flex: 1;
    }
   .chat-messages {
    scroll-behavior: smooth;
}

    .chat-user {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 14px 18px;
        cursor: pointer;
        transition: .2s;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
    }

    .chat-user:hover {
        background: #f5f5f5;
    }

    .chat-user.active {
        background: var(--purple-50);
    }

    .chat-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: var(--purple-800);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        flex-shrink: 0;
    }

    .chat-user-name {
        font-weight: 600;
    }

    .chat-user-email {
        color: #777;
        font-size: 13px;
    }

    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        height: 72px;
        border-bottom: var(--border);
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 0 25px;
        background: #f7f7f7;
    }

    .chat-messages {
        flex: 1;
        overflow: auto;
        padding: 25px;
        background: #efeae2;
    }

    .message-row {
        display: flex;
        width: 100%;
        margin-bottom: 12px;
    }

    .message-row.sent {
        justify-content: flex-end;
    }

    .message-row.received {
        justify-content: flex-start;
    }

    .message-bubble {
        max-width: 420px;
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .12);
    }

    .sent-bubble {
        background: #d9fdd3;
        border-bottom-right-radius: 4px;
    }

    .received-bubble {
        background: white;
        border-bottom-left-radius: 4px;
    }

    .message-time {
        margin-top: 5px;
        text-align: right;
        font-size: 11px;
        color: #777;
    }

    .chat-input {
        display: flex;
        gap: 15px;
        padding: 15px;
        border-top: var(--border);
        background: #fff;
    }

    .chat-input input {
        flex: 1;
        border: var(--border);
        border-radius: 999px;
        padding: 12px 18px;
        outline: none;
    }

    .chat-send {
        background: var(--purple-800);
        color: white;
        border: none;
        border-radius: 999px;
        padding: 12px 24px;
        cursor: pointer;
    }

    .chat-send:hover {
        background: var(--purple-600);
    }
    .header{
        display: none;
    }

    /*responxive */
    @media(max-width:768px){
    .header{
        display: block;
        padding: 10px;

    }
    .sidebar-avatar{
        margin-left: 0;
        
    }
    }


    /* ============================================================
       🎯 GROUP PAGE STYLES (SCOPED - ONLY AFFECT GROUP PAGES)
       ============================================================ */
    /* These styles ONLY apply when the parent has class 'group-page' */
    .group-page .group-dash-body {
        padding: 32px;
        max-width: 860px;
        margin: 0 auto;
    }

    .group-page .group-vertical-stack {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        margin-bottom: 32px;
    }

    .group-page .group-stat-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
        margin-bottom: 32px;
    }

    .group-page .group-discussion-stack {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        margin-bottom: 32px;
    }

    .group-page .group-reply-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
        margin-bottom: 32px;
    }

    .group-page .group-card {
        background: #ffffff;
        border-radius: var(--radius);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid var(--gray-200);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .group-page .group-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1, #818cf8);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .group-page .group-card:hover {
        box-shadow: 0 16px 48px rgba(0,0,0,0.15);
        transform: translateY(-4px);
        border-color: #818cf8;
    }

    .group-page .group-card:hover::before {
        opacity: 1;
    }

    .group-page .group-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .group-page .group-card-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .group-page .group-card-title .avatar {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #eef2ff;
        color: #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .group-page .group-card-title h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .group-page .group-card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-size: 0.85rem;
        flex-wrap: wrap;
    }

    .group-page .group-card-body {
        color: #475569;
        line-height: 1.7;
        margin-bottom: 18px;
        font-size: 0.95rem;
    }

    .group-page .group-card-body p {
        margin: 0;
    }

    .group-page .group-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: auto;
    }

    .group-page .group-card-footer .stats {
        display: flex;
        gap: 20px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .group-page .group-card-footer .stats span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .group-page .group-card-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .group-page .group-stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 28px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .group-page .group-stat-card:hover {
        box-shadow: 0 16px 48px rgba(0,0,0,0.15);
        transform: translateX(6px);
        border-color: #818cf8;
    }

    .group-page .group-stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .group-page .group-stat-card .stat-icon.blue {
        background: #eef2ff;
        color: #6366f1;
    }

    .group-page .group-stat-card .stat-icon.green {
        background: #ecfdf5;
        color: #22c55e;
    }

    .group-page .group-stat-card .stat-icon.orange {
        background: #fffbeb;
        color: #f59e0b;
    }

    .group-page .group-stat-card .stat-icon.purple {
        background: #f3e8ff;
        color: #8b5cf6;
    }

    .group-page .group-stat-card .stat-info {
        flex: 1;
    }

    .group-page .group-stat-card .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .group-page .group-stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .group-page .group-discussion-item {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .group-page .group-discussion-item:hover {
        box-shadow: 0 16px 48px rgba(0,0,0,0.15);
        transform: translateY(-4px);
        border-color: #818cf8;
    }

    .group-page .group-discussion-item .discussion-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .group-page .group-discussion-item .discussion-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        flex: 1;
    }

    .group-page .group-discussion-item .discussion-title a {
        color: inherit;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .group-page .group-discussion-item .discussion-title a:hover {
        color: #6366f1;
    }

    .group-page .group-discussion-item .discussion-excerpt {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 16px;
        font-size: 0.95rem;
    }

    .group-page .group-discussion-item .discussion-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: auto;
    }

    .group-page .group-discussion-item .discussion-meta {
        display: flex;
        align-items: center;
        gap: 18px;
        color: #64748b;
        font-size: 0.85rem;
        flex-wrap: wrap;
    }

    .group-page .group-discussion-item .discussion-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .group-page .group-reply-item {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px 24px;
        border-left: 4px solid #818cf8;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
    }

    .group-page .group-reply-item:hover {
        background: #f1f5f9;
        transform: translateX(6px);
    }

    .group-page .group-reply-item .reply-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .group-page .group-reply-item .reply-author {
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
    }

    .group-page .group-reply-item .reply-author .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #eef2ff;
        color: #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .group-page .group-reply-item .reply-time {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .group-page .group-reply-item .reply-body {
        color: #334155;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    .group-page .group-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .group-page .group-badge-primary {
        background: #eef2ff;
        color: #4f46e5;
    }

    .group-page .group-badge-success {
        background: #ecfdf5;
        color: #16a34a;
    }

    .group-page .group-badge-warning {
        background: #fffbeb;
        color: #b45309;
    }

    .group-page .group-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 22px;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 10px;
        border: 2px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .group-page .group-btn-primary {
        background: #6366f1;
        color: #fff;
        border-color: #6366f1;
        box-shadow: 0 8px 24px rgba(99,102,241,0.30);
    }

    .group-page .group-btn-primary:hover {
        background: #4f46e5;
        border-color: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(99,102,241,0.40);
    }

    .group-page .group-btn-outline {
        background: transparent;
        color: #334155;
        border-color: #cbd5e1;
    }

    .group-page .group-btn-outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateY(-2px);
    }

    .group-page .group-btn-sm {
        padding: 6px 16px;
        font-size: 0.813rem;
        border-radius: 8px;
    }

    .group-page .group-search-bar {
        margin-bottom: 28px;
        width: 100%;
    }

    .group-page .group-search-bar input {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
    }

    .group-page .group-search-bar input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
    }

    .group-page .group-share-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 0.813rem;
        font-weight: 600;
        color: #fff;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }

    .group-page .group-share-btn:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
    }

    .group-page .group-share-btn-whatsapp { background: #25D366; }
    .group-page .group-share-btn-twitter { background: #000; }
    .group-page .group-share-btn-facebook { background: #1877F2; }
    .group-page .group-share-btn-telegram { background: #26A5E4; }
    .group-page .group-share-btn-linkedin { background: #0A66C2; }

    /* Group animations */
    @keyframes groupSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .group-page .group-card,
    .group-page .group-discussion-item,
    .group-page .group-reply-item,
    .group-page .group-stat-card {
        animation: groupSlideUp 0.4s ease forwards;
    }

    .group-page .group-card:nth-child(1) { animation-delay: 0.04s; }
    .group-page .group-card:nth-child(2) { animation-delay: 0.08s; }
    .group-page .group-card:nth-child(3) { animation-delay: 0.12s; }
    .group-page .group-card:nth-child(4) { animation-delay: 0.16s; }
    .group-page .group-card:nth-child(5) { animation-delay: 0.20s; }
    .group-page .group-card:nth-child(6) { animation-delay: 0.24s; }

    .group-page .group-discussion-item:nth-child(1) { animation-delay: 0.04s; }
    .group-page .group-discussion-item:nth-child(2) { animation-delay: 0.08s; }
    .group-page .group-discussion-item:nth-child(3) { animation-delay: 0.12s; }
    .group-page .group-discussion-item:nth-child(4) { animation-delay: 0.16s; }

    .group-page .group-reply-item:nth-child(1) { animation-delay: 0.04s; }
    .group-page .group-reply-item:nth-child(2) { animation-delay: 0.08s; }
    .group-page .group-reply-item:nth-child(3) { animation-delay: 0.12s; }
    .group-page .group-reply-item:nth-child(4) { animation-delay: 0.16s; }

    .group-page .group-stat-card:nth-child(1) { animation-delay: 0.04s; }
    .group-page .group-stat-card:nth-child(2) { animation-delay: 0.08s; }
    .group-page .group-stat-card:nth-child(3) { animation-delay: 0.12s; }
    .group-page .group-stat-card:nth-child(4) { animation-delay: 0.16s; }

    /* Group responsive */
    @media (max-width: 768px) {
        .group-page .group-dash-body {
            padding: 16px;
        }
        
        .group-page .group-card {
            padding: 20px;
        }
        
        .group-page .group-discussion-item {
            padding: 18px 20px;
        }
        
        .group-page .group-reply-item {
            padding: 16px 18px;
        }
        
        .group-page .group-stat-card {
            padding: 18px 20px;
        }
        
        .group-page .group-card-header {
            flex-direction: column;
        }
        
        .group-page .group-card-footer {
            flex-direction: column;
            align-items: stretch;
        }
        
        .group-page .group-card-footer .stats {
            justify-content: space-around;
        }
        
        .group-page .group-discussion-item .discussion-header {
            flex-direction: column;
        }
        
        .group-page .group-discussion-item .discussion-footer {
            flex-direction: column;
            align-items: stretch;
        }
        
        .group-page .group-discussion-item .discussion-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
        
        .group-page .group-stat-card {
            flex-direction: row;
        }
    }

    @media (max-width: 480px) {
        .group-page .group-dash-body {
            padding: 12px;
        }
        
        .group-page .group-card {
            padding: 16px;
        }
        
        .group-page .group-card-title h3 {
            font-size: 1rem;
        }
        
        .group-page .group-card-footer .stats {
            flex-direction: column;
            gap: 6px;
        }
        
        .group-page .group-stat-card {
            flex-direction: column;
            text-align: center;
        }
        
        .group-page .group-stat-card .stat-icon {
            width: 44px;
            height: 44px;
            font-size: 1.2rem;
        }
        
        .group-page .group-stat-card .stat-value {
            font-size: 1.5rem;
        }
        
        .group-page .group-btn {
            font-size: 0.8rem;
            padding: 8px 16px;
        }
        
        .group-page .group-discussion-item .discussion-title {
            font-size: 1rem;
        }
        
        .group-page .group-reply-item .reply-author {
            font-size: 0.85rem;
        }
    }

    /* Group print styles */
    @media print {
        .group-page .dash-header-actions,
        .group-page .group-btn,
        .group-page .group-share-btn {
            display: none !important;
        }
        
        .group-page .group-card, 
        .group-page .group-discussion-item,
        .group-page .group-stat-card,
        .group-page .group-reply-item {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            transform: none !important;
            break-inside: avoid;
            margin-bottom: 16px;
        }
    }
/* ============================================================
   SIDEBAR - DESKTOP VIEW
   ============================================================ */


.sidebar-top {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.sidebar-bottom {
    border-top: 0.5px solid rgba(255, 255, 255, 0.08);
    padding-top: 4px;
}

/* Hide mobile nav on desktop */
.sidebar-mobile-nav {
    display: none;
}

/* ============================================================
   MOBILE NAVIGATION - BOTTOM BAR
   ============================================================ */
@media (max-width: 768px) {
    .dash-wrap {
        grid-template-columns: 1fr;
    }

    /* Hide desktop sidebar elements */
    .sidebar-logo {
        display: none;
    }

    .sidebar-user {
        display: none;
    }

    .sidebar-section-label {
        display: none;
    }

    .sidebar-section {
        display: none;
    }

    .sidebar-spacer {
        display: none;
    }

    /* Show mobile nav */
    .sidebar-mobile-nav {
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        width: 100%;
        padding: 4px 0;
        gap: 2px;
    }

    /* Sidebar becomes bottom navigation bar */
    .sidebar {
        display: flex;
        flex-direction: row;
        height: auto;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        top: auto;
        z-index: 1000;
        padding: 6px 8px;
        justify-content: center;
        align-items: center;
        background: var(--purple-900);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
    }

    .sidebar-top {
        display: none;
    }

    .sidebar-bottom {
        border-top: none;
        padding-top: 0;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    /* Mobile nav items */
    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        padding: 6px 10px;
        border-radius: var(--radius-sm);
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
        font-size: 10px;
        min-width: 44px;
        transition: all 0.2s ease;
        position: relative;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .mobile-nav-item i {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.2s ease;
    }

    .mobile-nav-item span {
        font-size: 8px;
        display: block;
        line-height: 1.2;
        max-width: 50px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: rgba(255, 255, 255, 0.4);
        transition: all 0.2s ease;
    }

    .mobile-nav-item.active {
        color: #fff;
    }

    .mobile-nav-item.active i {
        color: #fff;
    }

    .mobile-nav-item.active span {
        color: #fff;
    }

    .mobile-nav-item:hover {
        color: #fff;
    }

    .mobile-nav-item:hover i {
        color: #fff;
    }

    .mobile-nav-item:hover span {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Mobile badge */
    .mobile-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: #ef4444;
        color: #fff;
        font-size: 8px;
        font-weight: 600;
        padding: 1px 5px;
        border-radius: 10px;
        min-width: 16px;
        text-align: center;
        line-height: 1.4;
    }

    /* Mobile avatar */
    .mobile-avatar-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--purple-600);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .mobile-nav-item.active .mobile-avatar-circle {
        background: var(--purple-400);
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
    }

    .mobile-nav-item.mobile-avatar {
        padding: 4px 8px;
    }

    /* Main content padding for mobile (avoid footer overlap) */
    .dash-main {
        padding-bottom: 75px !important;
    }

    .group-dash-body {
        padding-bottom: 75px !important;
    }

    .dash-body {
        padding-bottom: 75px !important;
    }

    .auth-page .sidebar {
        display: none;
    }
}

/* ============================================================
   EXTRA SMALL SCREENS
   ============================================================ */
@media (max-width: 480px) {
    .mobile-nav-item {
        padding: 4px 6px;
        min-width: 36px;
    }

    .mobile-nav-item i {
        font-size: 18px;
    }

    .mobile-nav-item span {
        font-size: 7px;
        max-width: 35px;
    }

    .mobile-badge {
        font-size: 7px;
        padding: 1px 4px;
        min-width: 14px;
        top: 0;
        right: 0;
    }

    .mobile-avatar-circle {
        width: 24px;
        height: 24px;
        font-size: 10px;
    }

    .dash-main {
        padding-bottom: 65px !important;
    }

    .group-dash-body {
        padding-bottom: 65px !important;
    }
}

/* ============================================================
   TABLET VIEW
   ============================================================ */
@media (min-width: 769px) and (max-width: 1024px) {
    .dash-wrap {
        grid-template-columns: 180px 1fr;
    }

    .sidebar-item {
        padding: 6px 10px;
        font-size: 12px;
    }

    .sidebar-item i {
        font-size: 15px;
    }

    .sidebar-logo-name {
        font-size: 13px;
    }

    .sidebar-logo-sub {
        font-size: 10px;
    }
}
    </style>
    @stack('styles')
</head>

<body>
    @yield('body')
    
    @stack('scripts')
</body>

</html>