<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna | SIPAFIK</title>
    <style>
        :root {
            --green: #244b3c;
            --green-dark: #19382d;
            --cream: #f7f5ee;
            --gold: #c5a66a;
            --text: #26352e;
            --muted: #7d8981;
            --border: #e8e8df;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .topbar {
            background: var(--green-dark);
            color: white;
            padding: 20px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .brand { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .brand span { color: var(--gold); }

        .back {
            color: white;
            text-decoration: none;
            border: 1px solid #ffffff55;
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 13px;
        }

        .container {
            width: min(1200px, 92%);
            margin: 35px auto;
        }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 { margin: 0 0 7px; font-size: 28px; }
        .subtitle { color: var(--muted); font-size: 14px; }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat, .panel {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
        }

        .stat-label { color: var(--muted); font-size: 13px; }
        .stat-number { font-size: 27px; font-weight: 700; margin-top: 8px; }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text);
            outline-color: var(--gold);
        }

        button, .reset {
            border: 0;
            border-radius: 8px;
            padding: 11px 17px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
        }

        .btn-search { background: var(--green); color: white; }
        .reset { background: #eee; color: #47534b; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            text-align: left;
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #f0f0e9;
            font-size: 13px;
        }

        .person { font-weight: 650; }
        .email { color: var(--muted); font-size: 12px; margin-top: 4px; }

        .role {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 20px;
            background: #f1eee5;
            color: #665638;
            font-size: 11px;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .active { background: #e6f3e9; color: #287443; }
        .inactive { background: #fbeaea; color: #a33e3e; }

        .action-form { display: inline; }
        .btn-on { background: #e5f2e8; color: #287443; }
        .btn-off { background: #fbeaea; color: #a33e3e; }

        .alert {
            padding: 13px 16px;
            border-radius: 9px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .success { background: #e6f3e9; color: #287443; }
        .error { background: #fbeaea; color: #a33e3e; }

        .pagination {
            margin-top: 20px;
        }

        .pagination nav > div:first-child { margin-bottom: 12px; }

        @media(max-width: 850px) {
            .filters { grid-template-columns: 1fr 1fr; }
            .stats { gap: 9px; }
            .stat { padding: 14px; }
            .heading { align-items: start; flex-direction: column; }
        }

        @media(max-width: 520px) {
            .stats { grid-template-columns: 1fr; }
            .filters { grid-template-columns: 1fr; }
            h1 { font-size: 23px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">SIPA<span>FIK</span></div>
    <a href="{{ route('super-admin.dashboard') }}" class="back">
        ← Kembali ke Dashboard
    </a>
</header>

<main class="container">

    <div class="heading">
        <div>
            <h1>Manajemen Pengguna</h1>
            <div class="subtitle">
                Kelola akun seluruh pengguna yang terdaftar di SIPAFIK.
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <section class="stats">
        <div class="stat">
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-number">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="stat">
            <div class="stat-label">Akun Aktif</div>
            <div class="stat-number">{{ number_format($stats['active']) }}</div>
        </div>

        <div class="stat">
            <div class="stat-label">Akun Nonaktif</div>
            <div class="stat-number">{{ number_format($stats['inactive']) }}</div>
        </div>
    </section>

    <section class="panel">

        <form method="GET" action="{{ route('super-admin.users') }}" class="filters">

            <input
                type="text"
                name="search"
                placeholder="Cari nama, username, atau email..."
                value="{{ request('search') }}"
            >

            <select name="role">
                <option value="">Semua Role</option>
                @foreach($roles as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="campus_id">
                <option value="">Semua Kampus</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}" @selected((string) request('campus_id') === (string) $campus->id)>
                        {{ $campus->name }}
                    </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>

            <button type="submit" class="btn-search">Cari</button>
        </form>

        <div style="margin-bottom:18px;">
            <a href="{{ route('super-admin.users') }}" class="reset">Reset Filter</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Kampus</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="person">{{ $user->name }}</div>
                                <div class="email">{{ $user->email }}</div>
                            </td>

                            <td>{{ $user->username ?: '-' }}</td>

                            <td>
                                <span class="role">
                                    {{ $roles[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td>{{ $user->campus->name ?? '—' }}</td>

                            <td>
                                <span class="status {{ $user->is_active ? 'active' : 'inactive' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>{{ $user->created_at?->format('d M Y') ?? '-' }}</td>

                            <td>
                                @if($user->id !== auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('super-admin.users.toggle-status', $user->id) }}"
                                        class="action-form"
                                        onsubmit="return confirm('Yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="{{ $user->is_active ? 'btn-off' : 'btn-on' }}"
                                        >
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="email">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:35px;color:#7d8981;">
                                Tidak ada pengguna yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>

    </section>
</main>

</body>
</html>
