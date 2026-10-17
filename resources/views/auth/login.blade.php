@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="auth-center">
    <div class="auth-box">
        <div class="card page-card">
            <div class="card-body">
                <h1 class="page-title h4">Login</h1>
                <p class="small text-muted">Demo accounts (password: <strong>password</strong>): admin@food.test, donor@food.test, ngo@food.test</p>
                <p class="form-hint"><span class="text-danger">*</span> Required fields</p>
                <form method="POST" action="{{ url('/auth/login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-brand">Login</button>
                    </div>
                </form>
                <p class="mt-3 mb-0 small">No account? <a href="{{ url('/register') }}">Register</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
