<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Food Portal') — Community Food Wastage Reduction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-brand">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ url('/') }}">Food Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @yield('nav')
                </ul>
                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link nav-notifications position-relative" href="{{ url('/notifications') }}" title="Notifications">
                                <span class="nav-notifications-label">Alerts</span>
                                @if (($unreadNotificationCount ?? 0) > 0)
                                    <span class="nav-notifications-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <span class="navbar-text small text-white-50">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ url('/auth/logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link px-0">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1 w-100">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-3">
            @include('partials.unverified_banner')
        </div>

        @yield('content')
    </main>

    <footer class="border-top py-3 mt-auto">
        <div class="container text-center text-muted small">
            Community Food Wastage Reduction Portal &copy; {{ date('Y') }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
