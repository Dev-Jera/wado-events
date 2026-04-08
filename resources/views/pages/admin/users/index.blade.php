@extends('layouts.app')

@section('content')
    <section class="users-admin-page">
        <div class="users-admin-shell">
            <div class="users-head">
                <div>
                    <p>ACCESS CONTROL</p>
                    <h1>User Roles</h1>
                </div>
                <a href="{{ route('gate.portal') }}">Open Gate Portal</a>
            </div>

            @if ($isSuperAdmin)
                <form method="POST" action="{{ route('admin.users.storeAgent') }}" class="agent-create-card">
                    @csrf
                    <h2>Create Gate Agent</h2>
                    <div class="agent-grid">
                        <label>
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </label>
                        <label>
                            <span>Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                        </label>
                        <label>
                            <span>Phone</span>
                            <input type="text" name="phone" value="{{ old('phone') }}">
                        </label>
                        <label>
                            <span>Role</span>
                            <select name="role" required>
                                <option value="agent">AGENT</option>
                                <option value="gate">GATE</option>
                                <option value="gate_agent">GATE_AGENT</option>
                            </select>
                        </label>
                        <label>
                            <span>Password (optional)</span>
                            <input type="text" name="password" placeholder="Auto-generated if blank">
                        </label>
                    </div>
                    <button type="submit">Create Agent</button>
                </form>
            @endif

            <form method="GET" class="users-search">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search by name, email, phone">
                <button type="submit">Search</button>
            </form>

            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Current Role</th>
                            <th>Change Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: 'N/A' }}</td>
                                <td><span class="role-chip">{{ strtoupper((string) $user->role) }}</span></td>
                                <td>
                                    @if ($isSuperAdmin)
                                        <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="role-form">
                                            @csrf
                                            <select name="role">
                                                @foreach ($roleOptions as $role)
                                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ strtoupper($role) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit">Save</button>
                                        </form>
                                    @else
                                        <span class="readonly-note">Super admin only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="users-pagination">{{ $users->links() }}</div>
        </div>
    </section>

    <style>
        .users-admin-page { min-height: 100vh; background: #f3f7fd; padding: 8rem 1rem 3rem; }
        .users-admin-shell { width: min(1100px, calc(100% - 2rem)); margin: 0 auto; }
        .users-head { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: .85rem; }
        .users-head p { margin: 0; color: #000000; font-size: .72rem; letter-spacing: .12em; font-weight: 700; }
        .users-head h1 { margin: .3rem 0 0; color: #17365f; }
        .users-head a { text-decoration: none; color: #1f66d4; font-weight: 700; }
        .users-search { display: grid; grid-template-columns: 1fr auto; gap: .55rem; margin-bottom: .7rem; }
        .users-search input { height: 42px; border: 1px solid #cfdbec; border-radius: 10px; padding: 0 .72rem; }
        .users-search button { height: 42px; border: 0; border-radius: 10px; background: #1b66d5; color: #fff; font-weight: 700; padding: 0 .9rem; }
        .agent-create-card { margin-bottom: .75rem; border: 1px solid #d8e4f5; border-radius: 14px; background: #fff; padding: .85rem; display: grid; gap: .65rem; }
        .agent-create-card h2 { margin: 0; color: #1b3f6f; font-size: .98rem; }
        .agent-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .55rem; }
        .agent-grid label { display: grid; gap: .22rem; color: #3d5e86; font-size: .69rem; font-weight: 700; }
        .agent-grid input, .agent-grid select { height: 38px; border: 1px solid #cfdbec; border-radius: 8px; padding: 0 .6rem; }
        .agent-create-card button { width: fit-content; height: 36px; border: 0; border-radius: 8px; background: #117a4d; color: #fff; font-weight: 700; padding: 0 .85rem; }
        .users-table-wrap { overflow: auto; border: 1px solid #d8e4f5; border-radius: 14px; background: #fff; }
        .users-table { width: 100%; border-collapse: collapse; min-width: 860px; }
        .users-table th, .users-table td { border-bottom: 1px solid #e7eef8; padding: .62rem .66rem; text-align: left; color: #214064; font-size: .76rem; }
        .users-table th { background: #f5f9ff; color: #000000; font-size: .64rem; letter-spacing: .09em; text-transform: uppercase; }
        .role-chip { border-radius: 999px; padding: .18rem .45rem; border: 1px solid #cadcf7; background: #edf4ff; color: #2459a8; font-weight: 700; font-size: .62rem; }
        .role-form { display: flex; gap: .45rem; align-items: center; }
        .role-form select { height: 34px; border: 1px solid #cedbeb; border-radius: 8px; padding: 0 .45rem; }
        .role-form button { height: 34px; border: 0; border-radius: 8px; background: #1b66d5; color: #fff; font-size: .68rem; font-weight: 700; padding: 0 .62rem; }
        .readonly-note { color: #000000; font-weight: 700; font-size: .68rem; }
        .users-pagination { margin-top: .8rem; }
        @media (max-width: 1100px) { .agent-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 700px) {
            .users-search { grid-template-columns: 1fr; }
            .agent-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection
