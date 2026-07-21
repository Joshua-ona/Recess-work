{{--
    Reusable sidebar partial.
    Props (passed via @include):
      $role   — 'admin' | 'student' | 'lecturer'
      $user   — Auth user object
--}}

<nav class="sidebar" aria-label="Main navigation">

    <div class="sidebar-logo">
        <div class="sidebar-logo-row">
            <div class="sidebar-logo-icon" aria-hidden="true">
                <i class="ti ti-messages"></i>
            </div>
            <div>
                <div class="sidebar-logo-name">EduDiscuss</div>
                <div class="sidebar-logo-sub">
                    @if($role === 'admin') Administration
                    @elseif($role === 'lecturer') Lecturer portal
                    @else Student portal
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Admin nav ── --}}
    @if($role === 'admin')
        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i> Dashboard
            </a>
            <a href="{{ route('admin.analytics') }}"
               class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                <i class="ti ti-chart-bar" aria-hidden="true"></i> Analytics
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Management</div>
            <a href="{{ route('admin.users.index') }}"
               class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="ti ti-users" aria-hidden="true"></i> Users
                <span class="sidebar-badge">{{ $userCount ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.discussions') }}"
               class="sidebar-item {{ request()->routeIs('admin.discussions*') ? 'active' : '' }}">
                <i class="ti ti-messages" aria-hidden="true"></i> Discussions
            </a>
            <a href="{{ route('admin.courses') }}"
               class="sidebar-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                <i class="ti ti-books" aria-hidden="true"></i> Courses
            </a>
            <a href="{{ route('admin.reports') }}"
               class="sidebar-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="ti ti-flag" aria-hidden="true"></i> Reports
                @if(($openReports ?? 0) > 0)
                    <span class="sidebar-badge">{{ $openReports }}</span>
                @endif
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">System</div>
            <a href="{{ route('admin.settings') }}"
               class="sidebar-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="ti ti-settings" aria-hidden="true"></i> Settings
            </a>
            {{-- 🔐 Admin logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>

    {{-- ── Lecturer nav ── --}}
    @elseif($role === 'lecturer')
        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('lecturer.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('lecturer.dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i> Dashboard
            </a>
            
            <a href="{{ route('lecturer.discussions.index') }}" class="sidebar-item">
                <i class="ti ti-messages"></i> Discussions
            </a>

            <a href="{{ route('lecturer.categories') }}" class="sidebar-item">
                <i class="ti ti-category"></i> Categories
            </a>

            <a href="{{ route('lecturer.quizzes') }}" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> Quizzes
            </a>
            <a href="{{ route('lecturer.performance') }}" class="sidebar-item">
                <i class="ti ti-chart-bar"></i> Performance Reports
            </a>

          <a href="{{ route('chat') }}" class="sidebar-item">
    <i class="ti ti-message-circle"></i>
    Messages

    @if(($unreadMessages ?? 0) > 0)
        <span class="sidebar-badge">
            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
        </span>
    @endif
</a>

            <a href="{{ route('lecturer.notifications') }}" class="sidebar-item">
                <i class="ti ti-bell"></i> Notifications
            </a>

          
        </div>
        
          
        
        <div class="sidebar-section">
        
            {{-- 🔐 Lecturer logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>

    {{-- ── Student nav ── --}}
    @else
        <div class="sidebar-section">
            <div class="sidebar-section-label">My space</div>
            <a href="{{ route('student.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="ti ti-home" aria-hidden="true"></i> Home
            </a>
            <a href="{{ route('student.my-discussions') }}"
               class="sidebar-item {{ request()->routeIs('student.my-discussions') ? 'active' : '' }}">
                <i class="ti ti-messages" aria-hidden="true"></i> My discussions
                @if(($unreadCount ?? 0) > 0)
                    <span class="sidebar-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.saved') }}" class="sidebar-item">
                <i class="ti ti-bookmark" aria-hidden="true"></i> Saved posts
            </a>
        
            <a href="{{ route('student.quizzes') }}" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> Quizzes
            </a>

           <a href="{{ route('chat') }}" class="sidebar-item">
    <i class="ti ti-message-circle"></i>
    Messages

    @if(($unreadMessages ?? 0) > 0)
        <span class="sidebar-badge">
            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
        </span>
    @endif
</a>

            <a href="{{ route('groups.index') }}" class="sidebar-item">
                <i class="ti ti-users-group"></i> Groups
            </a>
        
       
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Account</div>
            
            <a href="{{ route('student.notifications') }}" class="sidebar-item">
                <i class="ti ti-bell" aria-hidden="true"></i> Notifications
                @if(($notifCount ?? 0) > 0)
                    <span class="sidebar-badge">{{ $notifCount }}</span>
                @endif
            </a>
            {{-- 🔐 Student logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>
    @endif

    <div class="sidebar-spacer"></div>

    {{-- User footer --}}
    <div class="sidebar-user">
        <div class="sidebar-avatar" aria-hidden="true">
            {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
        </div>
        <div>
            <div class="sidebar-user-name">{{ $user->name ?? ($user->first_name.' '.$user->last_name) }}</div>
            <div class="sidebar-user-meta">{{ $user->email }}</div>
        </div>
    </div>
     {{--icons--}}
    <div class="header">
           <a href="{{ route('student.dashboard') }}"
               class="sidebar-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="ti ti-home" aria-hidden="true"></i> 
            </a>
            <a href="{{ route('student.my-discussions') }}"
               class="sidebar-item {{ request()->routeIs('student.my-discussions') ? 'active' : '' }}">
                <i class="ti ti-messages" aria-hidden="true"></i> 
                @if(($unreadCount ?? 0) > 0)
                    <span class="sidebar-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.saved') }}" class="sidebar-item">
                <i class="ti ti-bookmark" aria-hidden="true"></i>  
            </a>
             <a href="{{ route('student.quizzes') }}" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> 
            </a>
            <a href="{{ route('chat') }}" class="sidebar-item">
                <i class="ti ti-message-circle"></i> 
            </a>
            <a href="{{ route('groups.index') }}" class="sidebar-item">
                <i class="ti ti-users-group"></i> 
            </a>
             <div class="sidebar-avatar" aria-hidden="true">
            {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
        </div>

    </div>

</nav>