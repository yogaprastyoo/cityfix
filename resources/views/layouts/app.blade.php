<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'CityFix')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                            <span class="badge badge-light">{{ \App\Models\User::ROLES[auth()->user()->role] ?? auth()->user()->role }}</span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('password.edit') }}" class="nav-link" title="Ganti Password"><i class="fas fa-key"></i></a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="{{ route('dashboard') }}" class="brand-link text-center">
                    <span class="brand-text">CITYFIX</span>
                </a>
                <div class="sidebar">
                    <div class="user-panel d-flex mb-3 mt-3 pb-3">
                        <div class="info"><a href="#" class="d-block">Bumi Sholawat</a></div>
                    </div>
                    @include('partials.sidebar-menu')
                </div>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">@yield('content-header')</div>
                </section>
                <section class="content">
                    <div class="container-fluid">
                        @include('partials.flash')
                        @yield('content')
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <strong>CityFix Bumi Sholawat</strong>
                <div class="d-none d-sm-inline-block float-right">v1.0.0</div>
            </footer>
        </div>
        @stack('scripts')
    </body>

</html>
