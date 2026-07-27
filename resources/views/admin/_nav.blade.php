{{-- Shared top nav for all admin pages (no sidebar) --}}
<div class="dash-header-actions" style="gap:8px; flex-wrap:wrap;">
    <a href="{{ route('admin.dashboard') }}"
        class="btn btn-sm {{ request()->routeIs('admin.dashboard') ? 'btn-primary' : 'btn-outline' }}">
        Dashboard
    </a>
    <a href="{{ route('admin.Users.index') }}"
        class="btn btn-sm {{ request()->routeIs('admin.Users.*') ? 'btn-primary' : 'btn-outline' }}">
        Manage users
    </a>
    <a href="{{ route('admin.lecturers.create') }}"
        class="btn btn-sm {{ request()->routeIs('admin.lecturers.*') ? 'btn-primary' : 'btn-outline' }}">
        Add lecturer
    </a>
    <a href="{{ route('admin.analytics') }}"
        class="btn btn-sm {{ request()->routeIs('admin.analytics') || request()->routeIs('admin.analytics.*') ? 'btn-primary' : 'btn-outline' }}">
        Analytics
    </a>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="icon-btn" aria-label="Log out">
            <i class="ti ti-logout" aria-hidden="true"></i>
        </button>
    </form>
</div>