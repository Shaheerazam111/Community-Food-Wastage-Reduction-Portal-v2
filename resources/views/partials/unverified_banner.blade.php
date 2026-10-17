@auth
    @if (in_array(auth()->user()->role, ['donor', 'recipient'], true) && ! auth()->user()->is_verified)
        <div class="alert alert-warning mb-0">
            <strong>Waiting for admin to verify your account.</strong>
            Upload your verification document on <a href="{{ url('/profile') }}">My profile</a>.
            You can browse the portal, but you cannot post or claim food until you are verified.
        </div>
    @endif
@endauth
