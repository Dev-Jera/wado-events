@extends('layouts.app')

@section('content')
    <section class="ua-page">
        <div class="ua-shell">
            <header class="ua-head">
                <div>
                    <h1>User roles</h1>
                    <p>Manage platform access levels for all registered users and agents.</p>
                </div>
                <a href="{{ route('gate.portal') }}" class="ua-head-link">Open gate portal</a>
            </header>

            @if (session('success'))
                <div class="ua-alert ua-alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="ua-alert ua-alert-error">
                    <p>Please fix the following errors:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="ua-stats">
                <article class="ua-stat ua-stat-primary">
                    <span>TOTAL USERS</span>
                    <strong>{{ number_format((int) $totalUsers) }}</strong>
                </article>
                <article class="ua-stat">
                    <span>CUSTOMERS</span>
                    <strong>{{ number_format((int) $customersCount) }}</strong>
                </article>
                <article class="ua-stat">
                    <span>ADMINS</span>
                    <strong>{{ number_format((int) $adminsCount) }}</strong>
                </article>
            </div>

            @if ($isSuperAdmin)
                <details class="ua-create" @if ($errors->any()) open @endif>
                    <summary>Create user or agent account</summary>
                    <form method="POST" action="{{ route('admin.users.storeAgent') }}" enctype="multipart/form-data" class="ua-create-form">
                        @csrf
                        <div class="ua-grid">
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
                                    @foreach ($createRoleOptions as $role)
                                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ strtoupper($role) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Password (optional)</span>
                                <input type="text" name="password" placeholder="Auto-generated if blank">
                            </label>
                            <label>
                                <span>Image</span>
                                <input type="file" name="profile_image" accept="image/*">
                            </label>
                        </div>
                        <button type="submit">Create account</button>
                    </form>
                </details>
            @endif

            <section class="ua-table-card">
                <div class="ua-table-top">
                    <div class="ua-all-users">All users</div>
                    <form method="GET" class="ua-search">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Search by name, email, phone">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <div class="ua-table-wrap">
                    <table class="ua-table">
                        <thead>
                            <tr>
                                <th>USER</th>
                                <th>EMAIL</th>
                                <th>PHONE</th>
                                <th>ROLE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="ua-user-cell">
                                            @if ($user->profile_image_path)
                                                <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="{{ $user->name }}" class="ua-avatar">
                                            @else
                                                <span class="ua-avatar ua-avatar-fallback">{{ strtoupper(substr((string) $user->name, 0, 2)) }}</span>
                                            @endif
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <small>Joined {{ optional($user->created_at)->format('M Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?: '—' }}</td>
                                    <td>
                                        <span class="ua-role-chip">{{ strtoupper((string) $user->role) }}</span>
                                    </td>
                                    <td>
                                        @if ($isSuperAdmin)
                                            <form method="POST" action="{{ route('admin.users.updateRole', $user) }}" class="ua-role-form">
                                                @csrf
                                                <select name="role">
                                                    @foreach ($roleOptions as $role)
                                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ strtoupper($role) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit">Save</button>
                                            </form>
                                        @else
                                            <span class="ua-readonly">Super admin only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="ua-empty">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="ua-pagination">{{ $users->links() }}</div>
        </div>
    </section>

    <style>
        .ua-page { min-height: 100vh; background: #f3f6fb; padding: 7.9rem 1rem 2.6rem; }
        .ua-shell { width: min(1060px, calc(100% - 2rem)); margin: 0 auto; display: grid; gap: .78rem; }
        .ua-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .ua-head h1 { margin: 0; font-size: 2rem; line-height: 1.1; color: #111827; }
        .ua-head p { margin: .33rem 0 0; color: #53709a; font-size: 1rem; }
        .ua-head-link { text-decoration: none; font-weight: 700; color: #0f4fa8; }

        .ua-alert { border-radius: 12px; padding: .68rem .8rem; font-size: .8rem; }
        .ua-alert-success { border: 1px solid #b9e7cb; background: #eefaf3; color: #156b3f; }
        .ua-alert-error { border: 1px solid #f5c2ca; background: #fff2f4; color: #96243a; }
        .ua-alert-error ul { margin: .35rem 0 0; padding-left: 1.15rem; }

        .ua-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .65rem; }
        .ua-stat { background: #fff; border: 1px solid #d6e0ee; border-radius: 14px; padding: .8rem .9rem; display: grid; gap: .2rem; }
        .ua-stat span { color: #5d7292; font-size: .74rem; font-weight: 700; letter-spacing: .05em; }
        .ua-stat strong { color: #1456b4; font-size: 2rem; line-height: 1; }
        .ua-stat-primary { background: #122554; border-color: #122554; }
        .ua-stat-primary span, .ua-stat-primary strong { color: #ffffff; }

        .ua-create { background: #ffffff; border: 1px solid #d6e0ee; border-radius: 14px; padding: .6rem .75rem .74rem; }
        .ua-create summary { cursor: pointer; color: #193e75; font-weight: 800; font-size: .85rem; }
        .ua-create-form { margin-top: .65rem; display: grid; gap: .55rem; }
        .ua-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .52rem; }
        .ua-grid label { display: grid; gap: .22rem; }
        .ua-grid span { font-size: .67rem; font-weight: 700; color: #2f4f77; letter-spacing: .03em; text-transform: uppercase; }
        .ua-grid input, .ua-grid select { height: 38px; border: 1px solid #cfdbec; border-radius: 9px; padding: 0 .6rem; font-size: .79rem; }
        .ua-create-form button { width: fit-content; border: none; border-radius: 9px; background: #157444; color: #fff; height: 36px; padding: 0 .85rem; font-weight: 700; }

        .ua-table-card { background: #fff; border: 1px solid #d6e0ee; border-radius: 16px; overflow: hidden; }
        .ua-table-top { background: #122554; padding: .75rem .85rem; display: flex; align-items: center; justify-content: space-between; gap: .8rem; }
        .ua-all-users { color: #fff; font-weight: 800; font-size: 1rem; line-height: 1; }
        .ua-search { display: grid; grid-template-columns: 1fr auto; gap: .45rem; width: min(500px, 100%); }
        .ua-search input { height: 40px; border-radius: 10px; border: 1px solid #3a4f84; background: #23386a; color: #f2f6ff; padding: 0 .72rem; }
        .ua-search input::placeholder { color: #c6d5f3; }
        .ua-search button { border: 1px solid #5470a8; height: 40px; border-radius: 10px; background: #1f66d5; color: #fff; font-weight: 700; padding: 0 .8rem; }

        .ua-table-wrap { overflow: auto; }
        .ua-table { width: 100%; border-collapse: collapse; min-width: 900px; }
        .ua-table th, .ua-table td { border-bottom: 1px solid #e3ebf6; padding: .72rem .78rem; text-align: left; color: #244064; font-size: .79rem; }
        .ua-table th { background: #f8fbff; color: #51698e; font-size: .68rem; letter-spacing: .06em; font-weight: 800; }
        .ua-user-cell { display: flex; align-items: center; gap: .58rem; }
        .ua-user-cell strong { display: block; color: #18345f; font-size: .86rem; }
        .ua-user-cell small { color: #7290b8; font-size: .73rem; }
        .ua-avatar { width: 34px; height: 34px; border-radius: 999px; object-fit: cover; border: 1px solid #d6e0ee; display: inline-flex; align-items: center; justify-content: center; }
        .ua-avatar-fallback { background: #1f66d5; color: #fff; font-size: .7rem; font-weight: 800; }
        .ua-role-chip { display: inline-flex; border-radius: 999px; padding: .22rem .46rem; font-size: .63rem; font-weight: 800; border: 1px solid #c5d6ef; background: #ebf2ff; color: #1a4f9c; }
        .ua-role-form { display: flex; align-items: center; gap: .38rem; }
        .ua-role-form select { height: 34px; border: 1px solid #cedbeb; border-radius: 8px; padding: 0 .45rem; font-size: .68rem; }
        .ua-role-form button { height: 34px; border: none; border-radius: 8px; background: #1b66d5; color: #fff; font-size: .68rem; font-weight: 700; padding: 0 .62rem; }
        .ua-readonly { font-size: .67rem; color: #6d85a7; font-weight: 700; }
        .ua-empty { color: #7088ab; text-align: center; }
        .ua-pagination { margin-top: .66rem; }

        @media (max-width: 980px) {
            .ua-stats { grid-template-columns: 1fr; }
            .ua-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 720px) {
            .ua-head { display: grid; gap: .4rem; }
            .ua-grid { grid-template-columns: 1fr; }
            .ua-table-top { display: grid; }
            .ua-search { width: 100%; }
        }
    </style>
@endsection
