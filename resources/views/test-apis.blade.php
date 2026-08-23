<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Tester — Food Wastage Portal v2</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="{{ asset('css/api-tester.css') }}">
</head>
<body>

@include('test-apis.partials.session-widget')

<div class="wrap">
    <header>
        <h1>Community Food Wastage Portal — API Tester</h1>
        <p>Interactive test UI for all Postman APIs · maps assignment tasks to live endpoints</p>
    </header>

    <div class="card">
        <h2>Assignment workflow</h2>
        <div class="flow">
            <span class="flow-step">Task 1: Register & Login</span>
            <span class="flow-arrow">→</span>
            <span class="flow-step">Task 2: Donor creates listing (AVAILABLE)</span>
            <span class="flow-arrow">→</span>
            <span class="flow-step">Task 3: Public nearby search</span>
            <span class="flow-arrow">→</span>
            <span class="flow-step">Task 4: Recipient claims (CLAIMED)</span>
            <span class="flow-arrow">→</span>
            <span class="flow-step">Task 2: Donor delivers (DELIVERED)</span>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>Shared session state</h2>
            <p class="api-desc">Values auto-update when you call APIs (like Postman variables).</p>
            <div class="state-row">
                <div class="state-item">
                    <label>Logged in as</label>
                    <span id="session-user">— none —</span>
                </div>
                <div class="state-item">
                    <label>listing_id (used for claim / deliver)</label>
                    <div class="listing-id-row">
                        <div>
                            <select id="listing-id-select" style="width:100%">
                                <option value="">Select listing…</option>
                            </select>
                        </div>
                        <div class="id-manual">
                            <input type="number" id="listing-id" placeholder="ID">
                        </div>
                    </div>
                </div>
            </div>
            <div class="quick-btns">
                <button class="btn btn-sm btn-primary" data-quick-login="donor1@test.com">Quick login: Donor</button>
                <button class="btn btn-sm btn-primary" data-quick-login="ngoA@test.com">Quick login: Recipient A</button>
                <button class="btn btn-sm btn-primary" data-quick-login="ngoB@test.com">Quick login: Recipient B</button>
                <button class="btn btn-sm" style="background:var(--surface2);color:var(--text)" id="btn-refresh">Refresh tables</button>
            </div>
        </div>
        <div class="card">
            <h2>Status lifecycle</h2>
            <div class="flow">
                <span class="flow-step status status-AVAILABLE">AVAILABLE</span>
                <span class="flow-arrow">→ claim</span>
                <span class="flow-step status status-CLAIMED">CLAIMED</span>
                <span class="flow-arrow">→ deliver</span>
                <span class="flow-step status status-DELIVERED">DELIVERED</span>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>Users in database</h2>
            <div style="overflow-x:auto">
                <table id="users-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Location</th></tr></thead>
                    <tbody><tr><td colspan="5" class="empty">Loading…</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <h2>Food listings</h2>
            <div style="overflow-x:auto">
                <table id="listings-table">
                    <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Donor</th><th>Claimed by</th><th>Expiry</th></tr></thead>
                    <tbody><tr><td colspan="6" class="empty">Loading…</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Map — search & listings</h2>
        <p class="api-desc">Blue = search center · Green = available · Orange = claimed</p>
        <div id="map"></div>
    </div>

    <h2 style="margin:1.5rem 0 .75rem">API endpoints (Postman collection)</h2>

    @foreach ($apiGroups as $group)
        <div class="group" style="--group-color: {{ $group['color'] }}" data-group="{{ $group['id'] }}">
            <div class="group-header" onclick="toggleGroup(this.parentElement)">
                <div>
                    <h2>{{ $group['title'] }}</h2>
                    <div class="group-meta">
                        <span class="badge badge-task">{{ $group['task'] }}</span>
                        @if (!empty($group['challenge']))
                            <span class="badge badge-challenge">{{ $group['challenge'] }}</span>
                        @endif
                        — {{ $group['description'] }}
                    </div>
                </div>
                <span class="chevron">▶</span>
            </div>
            <div class="group-body">
                @if ($group['id'] === 'claim')
                    <div class="card" style="margin:.5rem 0;background:#1c1010;border-color:#dc2626">
                        <h2>Available listings to claim</h2>
                        <p class="api-desc">Click a listing to set <code>listing_id</code>, then run Claim below.</p>
                        <div class="claimable-list" id="claimable-listings">
                            <span class="empty">Loading listings…</span>
                        </div>
                    </div>
                @endif
                @foreach ($group['apis'] as $api)
                    @include('test-apis.partials.api-panel', ['api' => $api])
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script type="module" src="{{ asset('js/api-tester/app.js') }}"></script>
</body>
</html>
