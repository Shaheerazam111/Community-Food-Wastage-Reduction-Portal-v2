<li class="nav-item"><a class="nav-link {{ request()->is('donor/dashboard') ? 'active' : '' }}" href="{{ url('/donor/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('donor/donations/create') ? 'active' : '' }}" href="{{ url('/donor/donations/create') }}">Post food</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('food/mine') ? 'active' : '' }}" href="{{ url('/food/mine') }}">My listings</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">My profile</a></li>
