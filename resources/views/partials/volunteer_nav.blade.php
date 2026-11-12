<li class="nav-item"><a class="nav-link {{ request()->is('volunteer/tasks') ? 'active' : '' }}" href="{{ url('/volunteer/tasks') }}">Pickup tasks</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('notifications') ? 'active' : '' }}" href="{{ url('/notifications') }}">Notifications</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">Profile</a></li>
