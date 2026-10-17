<li class="nav-item"><a class="nav-link {{ request()->is('recipient/dashboard') ? 'active' : '' }}" href="{{ url('/recipient/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('recipient/food', 'food/search') ? 'active' : '' }}" href="{{ url('/recipient/food') }}">Find food</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('food/claims', 'recipient/claims') ? 'active' : '' }}" href="{{ url('/food/claims') }}">My claims</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">My profile</a></li>
