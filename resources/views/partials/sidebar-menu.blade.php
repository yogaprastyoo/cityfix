@php($role = auth()->user()->role)
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-header">LAPORAN</li>
        @if (in_array($role, ['admin', 'verifier', 'reporter']))
            <li class="nav-item">
                <a href="{{ route('reports.create') }}" class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-plus-circle"></i>
                    <p>Buat Laporan</p>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index', 'reports.show') ? 'active' : '' }}">
                <i class="nav-icon fas {{ $role === 'technician' ? 'fa-tasks' : 'fa-clipboard-list' }}"></i>
                <p>{{ ['reporter' => 'Laporan Saya', 'technician' => 'My Task'][$role] ?? 'Daftar Laporan' }}</p>
            </a>
        </li>

        @if (in_array($role, ['admin', 'verifier']))
            <li class="nav-header">MONITORING</li>
            <li class="nav-item">
                <a href="{{ route('monitoring.index') }}" class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-map-marked-alt"></i>
                    <p>Area Monitoring</p>
                </a>
            </li>
        @endif

        @if ($role === 'admin')
            <li class="nav-header">MASTER DATA</li>
            <li class="nav-item">
                <a href="{{ route('areas.index') }}" class="nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-building"></i>
                    <p>Area</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tags"></i>
                    <p>Kategori</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>User</p>
                </a>
            </li>
        @endif
    </ul>
</nav>
