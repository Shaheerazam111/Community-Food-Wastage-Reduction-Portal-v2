<li class="nav-item"><a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/reports') ? 'active' : '' }}" href="{{ url('/admin/reports') }}">Reports</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/listings') ? 'active' : '' }}" href="{{ url('/admin/listings') }}">Listings</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}" href="{{ url('/admin/categories') }}">Categories</a></li>
<li class="nav-item"><a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ url('/admin/users') }}">Users</a></li>
