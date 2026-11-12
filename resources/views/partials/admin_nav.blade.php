<li class="nav-item"><a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">Reports</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">Users</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/verification*') ? 'active' : '' }}" href="{{ route('admin.verification') }}">NGO docs</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/listings*') ? 'active' : '' }}" href="{{ route('admin.listings') }}">Moderate food</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('notifications') ? 'active' : '' }}" href="{{ url('/notifications') }}">Notifications</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ url('/profile') }}">Profile</a></li>
