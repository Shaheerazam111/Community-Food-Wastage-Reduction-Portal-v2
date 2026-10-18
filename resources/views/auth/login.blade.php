@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="auth-center">
    <div class="auth-box">
        <div class="card page-card">
            <div class="card-body">
                <h1 class="page-title h4">Log in</h1>
                <p class="text-muted small mb-3">
                    Use your email and password. You will go straight to your role dashboard (donor, NGO, or admin).
                </p>
                <p class="form-hint"><span class="text-danger">*</span> Required fields</p>
                <form method="POST" action="{{ url('/auth/login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-brand">Log in</button>
                    </div>
                </form>

                <details class="ui-disclose mt-3">
                    <summary>Demo accounts (after <code>php artisan db:seed</code>)</summary>
                    <div class="ui-disclose-body">
                        <p class="small text-muted mb-2">Password for all: <strong>password</strong></p>
                        <ul class="small text-muted mb-0">
                            <li><code>admin@food.test</code> — admin dashboard</li>
                            <li><code>donor@food.test</code> — donor dashboard</li>
                            <li><code>ngo@food.test</code> — NGO dashboard</li>
                        </ul>
                        <p class="small text-muted mt-2 mb-0">More accounts: <code>demo-test-data.md</code></p>
                    </div>
                </details>

                <p class="mt-3 mb-0 small">No account? <a href="{{ url('/register') }}">Register</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
