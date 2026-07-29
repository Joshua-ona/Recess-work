{{--
    Reusable sidebar partial.
    Props (passed via @include):
      $role   — 'system_admin' | 'student' | 'lecturer'
      $user   — Auth user object
--}}

<nav class="sidebar" aria-label="Main navigation">

    {{-- TOP SECTION --}}
    <div class="sidebar-top">

        {{-- Logo (visible on desktop) --}}
        <div class="sidebar-logo">
            <div class="sidebar-logo-row">
                 <img src="{{ asset('images/logo.png') }}"
             width="30"
             height="30"
             alt="EduDiscuss Logo">
               
                <div>
                    <div class="sidebar-logo-name">EduDiscuss</div>
                    <div class="sidebar-logo-sub">
                        @if($role === 'system_admin') Administration
                        @elseif($role === 'lecturer') Lecturer portal
                        @else Student portal
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Admin nav ── --}}
        @if($role === 'system_admin')
            <div class="sidebar-section">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard" aria-hidden="true"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.analytics') }}"
                   class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar" aria-hidden="true"></i> <span>Analytics</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="ti ti-users" aria-hidden="true"></i> <span>Users</span>
                    <span class="sidebar-badge">{{ $userCount ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.lecturers.create') }}"
                   class="sidebar-item {{ request()->routeIs('admin.lecturers.*') ? 'active' : '' }}">
                    <i class="ti ti-user-plus" aria-hidden="true"></i> <span>Add lecturer</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-item logout-btn">
                        <i class="ti ti-logout" aria-hidden="true"></i> <span>Logout</span>
                    </button>
                </form>
            </div>

        {{-- ── Lecturer nav ── --}}
        @elseif($role === 'lecturer')
            <div class="sidebar-section">
                <div class="sidebar-section-label">Overview</div>
                <a href="{{ route('lecturer.dashboard') }}"
                   class="sidebar-item {{ request()->routeIs('lecturer.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard" aria-hidden="true"></i> <span>Dashboard</span>
                </a>
    
                <a href="{{ route('lecturer.quizzes') }}" class="sidebar-item">
                    <i class="ti ti-clipboard-check"></i> <span>Quizzes</span>
                </a>
                <a href="{{ route('lecturer.performance') }}" class="sidebar-item">
                    <i class="ti ti-chart-bar"></i> <span>Performance Reports</span>
                </a>
                <a href="{{ route('chat') }}" class="sidebar-item">
                    <i class="ti ti-message-circle"></i> <span>Messages</span>
                    @if(($unreadMessages ?? 0) > 0)
                        <span class="sidebar-badge">
                            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('lecturer.notifications') }}" class="sidebar-item">
                    <i class="ti ti-bell"></i> <span>Notifications</span>
                </a>
            </div>
            <div class="sidebar-section">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-item logout-btn">
                        <i class="ti ti-logout" aria-hidden="true"></i> <span>Logout</span>
                    </button>
                </form>
            </div>

        {{-- ── Student nav ── --}}
        @else
            <div class="sidebar-section">
                <div class="sidebar-section-label">My space</div>
                <a href="{{ route('student.dashboard') }}"
                   class="sidebar-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="ti ti-home" aria-hidden="true"></i> <span>Home</span>
                </a>
                <a href="{{ route('student.my-discussions') }}"
                   class="sidebar-item {{ request()->routeIs('student.my-discussions') ? 'active' : '' }}">
                    <i class="ti ti-messages" aria-hidden="true"></i> <span>My discussions</span>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="sidebar-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                
                <a href="{{ route('student.quizzes') }}" class="sidebar-item">
                    <i class="ti ti-clipboard-check"></i> <span>Quizzes</span>
                </a>
                <a href="{{ route('analytics.mine') }}"
                   class="sidebar-item {{ request()->routeIs('analytics.mine') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar" aria-hidden="true"></i> <span>My analytics</span>
                </a>
                <a href="{{ route('chat') }}" class="sidebar-item">
                    <i class="ti ti-message-circle"></i> <span>Messages</span>
                    @if(($unreadMessages ?? 0) > 0)
                        <span class="sidebar-badge">
                            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('groups.index') }}" class="sidebar-item {{ request()->routeIs('groups*') ? 'active' : '' }}">
                    <i class="ti ti-users-group"></i> <span>Groups</span>
                </a>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-section-label">Account</div>
                <a href="{{ route('student.notifications') }}" class="sidebar-item">
                    <i class="ti ti-bell" aria-hidden="true"></i> <span>Notifications</span>
                    @if(($notifCount ?? 0) > 0)
                        <span class="sidebar-badge">{{ $notifCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-item logout-btn">
                        <i class="ti ti-logout" aria-hidden="true"></i> <span>Logout</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- SPACER --}}
    <div class="sidebar-spacer"></div>

    {{-- BOTTOM SECTION --}}
    <div class="sidebar-bottom">

        {{-- User footer (visible on desktop) --}}
        <div class="sidebar-user">
            <div class="sidebar-avatar" aria-hidden="true">
                {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="sidebar-user-name">{{ $user->name ?? ($user->first_name.' '.$user->last_name) }}</div>
                <div class="sidebar-user-meta">{{ $user->email }}</div>
            </div>
        </div>

        {{-- ============================================================
            MOBILE NAVIGATION - ICONS ONLY (visible on mobile)
            ============================================================ --}}
        <div class="sidebar-mobile-nav">

            {{-- Dashboard / Home --}}
            <a href="{{ route('student.dashboard') }}"
               class="mobile-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="ti ti-home" aria-hidden="true"></i>
                <span>Home</span>
            </a>

            {{-- Discussions --}}
            <a href="{{ route('student.my-discussions') }}"
               class="mobile-nav-item {{ request()->routeIs('student.my-discussions') ? 'active' : '' }}">
                <i class="ti ti-messages" aria-hidden="true"></i>
                <span>Discussions</span>
                @if(($unreadCount ?? 0) > 0)
                    <span class="mobile-badge">{{ $unreadCount }}</span>
                @endif
            </a>

            {{-- Quizzes --}}
            <a href="{{ route('student.quizzes') }}" class="mobile-nav-item">
                <i class="ti ti-clipboard-check"></i>
                <span>Quiz</span>
            </a>

            {{-- Messages --}}
            <a href="{{ route('chat') }}" class="mobile-nav-item">
                <i class="ti ti-message-circle"></i>
                <span>Chat</span>
                @if(($unreadMessages ?? 0) > 0)
                    <span class="mobile-badge">
                        {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                    </span>
                @endif
            </a>

            {{-- Groups --}}
            <a href="{{ route('groups.index') }}" class="mobile-nav-item {{ request()->routeIs('groups*') ? 'active' : '' }}">
                <i class="ti ti-users-group"></i>
                <span>Groups</span>
            </a>

            {{-- Profile / Avatar --}}
            <a href="{{ route('student.dashboard') }}" class="mobile-nav-item mobile-avatar">
                <div class="mobile-avatar-circle">
                    {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}
                </div>
                <span>Profile</span>
            </a>

        </div>
    </div>

</nav>

@if($role === 'student')
<div id="live-quiz-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:2rem; max-width:380px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="font-size:38px; margin-bottom:.5rem;">⏰</div>
        <h3 style="margin:0 0 .5rem; font-size:18px; font-weight:700;">A quiz is now live</h3>
        <p id="live-quiz-title" style="color:#6b6a66; font-size:14px; margin-bottom:1.25rem;"></p>
        <a id="live-quiz-start-btn" href="#" class="btn btn-primary" style="width:100%; justify-content:center;">
            Start now
        </a>
    </div>
</div>

<script>
(function () {
    var checkUrl = "{{ route('student.quizzes.active-check') }}";
    var modal = document.getElementById('live-quiz-modal');
    var titleEl = document.getElementById('live-quiz-title');
    var startBtn = document.getElementById('live-quiz-start-btn');
    var AUTO_START_SECONDS = 15; // auto-redirects into the quiz if ignored
    var shown = false;

    function poll() {
        if (shown) return; // already showing/handled one this session
        fetch(checkUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.active) {
                    shown = true;
                    titleEl.textContent = '"' + data.title + '" has started — you have limited time to begin.';
                    startBtn.href = data.start_url;
                    modal.style.display = 'flex';

                    var remaining = AUTO_START_SECONDS;
                    var iv = setInterval(function () {
                        remaining--;
                        startBtn.textContent = 'Start now (' + remaining + ')';
                        if (remaining <= 0) {
                            clearInterval(iv);
                            window.location.href = data.start_url;
                        }
                    }, 1000);
                }
            })
            .catch(function () { /* ignore transient errors, try again next poll */ });
    }

    poll();
    setInterval(poll, 15000);
})();
</script>
@endif