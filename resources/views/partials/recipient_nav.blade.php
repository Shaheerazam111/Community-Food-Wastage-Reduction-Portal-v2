<li class="nav-item"><a class="nav-link {{ request()->is('recipient/food', 'food/search') ? 'active' : '' }}" href="{{ url('/recipient/food') }}">Find food</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('food/claims', 'recipient/claims') ? 'active' : '' }}" href="{{ url('/food/claims') }}">My claims</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('recipient/verification') ? 'active' : '' }}" href="{{ url('/recipient/verification') }}">Verification</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('notifications') ? 'active' : '' }}" href="{{ url('/notifications') }}">Notifications</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">Profile</a></li>
