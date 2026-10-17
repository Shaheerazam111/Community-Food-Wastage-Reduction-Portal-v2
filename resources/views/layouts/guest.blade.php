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
<body class="d-flex flex-column min-vh-100 bg-page">
    <nav class="navbar navbar-dark bg-brand">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ url('/') }}">Food Portal</a>
            <div class="d-flex align-items-center gap-2">
                <a class="nav-auth-btn nav-auth-btn-login" href="{{ url('/login') }}">Login</a>
                <a class="nav-auth-btn nav-auth-btn-register" href="{{ url('/register') }}">Register</a>
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

        @yield('content')
    </main>

    <footer class="border-top py-3 mt-auto bg-white">
        <div class="container text-center text-muted small">
            Community Food Wastage Reduction Portal &copy; {{ date('Y') }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
