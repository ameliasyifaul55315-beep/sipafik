
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — SIPAFIK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --forest: #173b32;
            --forest-dark: #102b25;
            --forest-soft: #285447;
            --cream: #f5f2e9;
            --paper: #fffdf8;
            --gold: #c5a66b;
            --sage: #dce6d9;
            --text: #24352f;
            --muted: #84918a;
            --line: #e9e8df;
            --red: #b65d55;
            --green: #47785c;
            --shadow: 0 12px 35px rgba(24, 49, 39, .06);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        a { color: inherit; text-decoration: none; }
        button, input { font: inherit; }

        .layout { min-height: 100vh; }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 258px;
            padding: 30px 20px;
            background:
                radial-gradient(circle at 10% 5%, rgba(197,166,107,.15), transparent 30%),
                linear-gradient(165deg, var(--forest), var(--forest-dark));
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 38px;
        }

        .brand-mark {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(220,196,145,.6);
            border-radius: 14px;
            color: #e3c991;
            font-family: 'Playfair Display', serif;
            font-size: 25px;
        }

        .brand-name {
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 17px;
        }

        .brand small {
            display: block;
            color: #b9c9bf;
            font-size: 9px;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .nav-label {
            color: #91aa9c;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.8px;
            padding: 0 12px;
            margin: 12px 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin: 3px 0;
            border-radius: 11px;
            color: #c6d3ca;
            font-size: 13px;
            transition: .25s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,.11);
            color: #fff;
        }

        .nav-link.active {
            box-shadow: inset 3px 0 var(--gold);
        }

        .nav-icon {
            width: 20px;
            text-align: center;
            color: #d5bd8a;
        }

        .sidebar-bottom { margin-top: auto; }

        .profile-mini {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 15px 8px;
            border-top: 1px solid rgba(255,255,255,.13);
            margin-bottom: 12px;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #d7c08e;
            color: var(--forest-dark);
            font-weight: 700;
        }

        .profile-mini strong { font-size: 12px; display: block; }
        .profile-mini small { color: #aabdb1; font-size: 10px; }

        .logout-button {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 10px;
            background: transparent;
            color: #e5caca;
            cursor: pointer;
            font-size: 12px;
            transition: .2s;
        }

        .logout-button:hover { background: rgba(255,255,255,.1); }

        .main {
            margin-left: 258px;
            padding: 30px 38px 24px;
            max-width: 1700px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 28px;
        }

        .eyebrow {
            color: #9b8557;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .date-text {
            color: var(--muted);
            font-size: 12px;
            margin: 6px 0 0;
        }

        .platform-pill {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 10px 16px;
            color: var(--forest);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .welcome {
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            min-height: 205px;
            padding: 35px 40px;
            border-radius: 20px;
            background:
                radial-gradient(circle at 85% 20%, rgba(197,166,107,.22), transparent 25%),
                linear-gradient(115deg, #173b32, #285447);
            color: white;
            box-shadow: var(--shadow);
            animation: rise .55s ease both;
        }

        .welcome h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(27px, 3vw, 38px);
            font-weight: 500;
            margin: 13px 0 10px;
        }

        .welcome p {
            max-width: 590px;
            color: #d2ded5;
            font-size: 13px;
            line-height: 1.8;
            margin: 0;
        }

        .welcome-mark {
            font-family: 'Playfair Display', serif;
            font-size: 112px;
            line-height: 1;
            color: rgba(230,207,158,.28);
            padding-right: 20px;
        }

        .alerts { margin-top: 18px; }

        .alert {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 9px;
            font-size: 13px;
        }

        .alert.success { background: #e2eee3; color: #326344; }
        .alert.error { background: #f6e5e2; color: #9d4942; }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
            margin: 34px 0 17px;
        }

        .section-heading h2 {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            font-weight: 600;
            margin: 7px 0 0;
            color: var(--forest);
        }

        .section-caption { color: var(--muted); font-size: 11px; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
        }

        .stat-card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: transform .25s, box-shadow .25s;
            animation: rise .6s ease both;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(24,49,39,.1);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--sage);
            color: var(--forest);
            font-size: 17px;
        }

        .stat-card:nth-child(2) .stat-icon { background: #f0e8d7; color: #94733e; }
        .stat-card:nth-child(3) .stat-icon { background: #f7e9dc; color: #a66d42; }
        .stat-card:nth-child(4) .stat-icon { background: #e4eee7; color: #47785c; }

        .stat-value {
            font-family: 'Playfair Display', serif;
            color: var(--forest);
            font-size: 32px;
            margin: 17px 0 4px;
        }

        .stat-label { color: var(--muted); font-size: 11px; }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
            gap: 17px;
            margin-top: 20px;
        }

        .panel {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 23px;
            box-shadow: var(--shadow);
            min-width: 0;
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 22px;
        }

        .panel-title h3 {
            margin: 0;
            color: var(--forest);
            font-size: 14px;
        }

        .panel-title span { color: var(--muted); font-size: 10px; }

        .role-row { margin: 17px 0; }

        .role-info {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .role-info strong { color: var(--forest); }
        .role-info span { color: var(--muted); }

        .bar-track {
            height: 8px;
            overflow: hidden;
            background: #edf0e9;
            border-radius: 20px;
        }

        .bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, #285447, #c5a66b);
            animation: grow .9s ease both;
        }

        .status-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
        }

        .status-box {
            padding: 15px 10px;
            border-radius: 12px;
            background: #f5f5ef;
            text-align: center;
        }

        .status-box strong {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            color: var(--forest);
        }

        .status-box span { color: var(--muted); font-size: 10px; }

        .status-box.pending { background: #f7efdf; }
        .status-box.approved { background: #e7f0e7; }
        .status-box.rejected { background: #f6e8e5; }

        .campus-list { display: grid; gap: 13px; }

        .campus-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
            background: #fffefa;
            transition: .2s;
        }

        .campus-card:hover { border-color: #cdbb94; }

        .campus-main {
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }

        .campus-avatar {
            flex: 0 0 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: var(--sage);
            color: var(--forest);
            font-family: 'Playfair Display', serif;
            font-size: 20px;
        }

        .campus-info { min-width: 0; flex: 1; }

        .campus-info h3 {
            margin: 1px 0 5px;
            color: var(--forest);
            font-size: 14px;
        }

        .campus-info p, .campus-info small {
            display: block;
            color: var(--muted);
            font-size: 11px;
            overflow-wrap: anywhere;
            margin: 3px 0;
        }

        .campus-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 9px;
            margin: 13px 0;
        }

        .status {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 700;
            background: #eee;
        }

        .status-pending { background: #f7edda; color: #98733c; }
        .status-approved { background: #e4efe5; color: #477354; }
        .status-rejected { background: #f5e5e2; color: #a45149; }

        .contact-meta { color: var(--muted); font-size: 10px; }

        .document-link {
            color: #8b7145;
            font-size: 11px;
            font-weight: 700;
        }

        .document-link:hover { text-decoration: underline; }

        .campus-actions {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 9px;
            padding-top: 13px;
            border-top: 1px solid var(--line);
        }

        .campus-actions form { display: flex; gap: 7px; }

        .campus-actions input {
            width: 190px;
            min-width: 0;
            padding: 9px 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            outline: none;
            font-size: 11px;
        }

        .campus-actions input:focus { border-color: var(--gold); }

        .approve-button, .reject-button {
            border: 0;
            border-radius: 8px;
            padding: 9px 12px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            transition: .2s;
        }

        .approve-button { background: var(--forest); color: white; }
        .approve-button:hover { background: #2d5a4a; }
        .reject-button { background: #f3e5e2; color: #a34f47; }
        .reject-button:hover { background: #ead2ce; }

        .rejection-note {
            color: #a45149;
            background: #fbefed;
            padding: 10px;
            border-radius: 8px;
            font-size: 11px;
        }

        .empty-state {
            text-align: center;
            padding: 35px 15px;
            color: var(--muted);
            background: #faf9f4;
            border-radius: 12px;
        }

        .empty-state strong {
            display: block;
            color: var(--forest);
            margin-bottom: 6px;
        }

        .user-list { display: grid; }

        .user-row {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .user-row:last-child { border-bottom: 0; }

        .user-avatar {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #e7ece3;
            color: var(--forest);
            font-weight: 700;
            font-size: 11px;
        }

        .user-detail { min-width: 0; flex: 1; }
        .user-detail strong { display: block; font-size: 11px; color: var(--text); }
        .user-detail small { display: block; font-size: 10px; color: var(--muted); overflow-wrap: anywhere; }
        .user-role { color: #8c774f; font-size: 10px; text-align: right; }

        .footer {
            padding: 28px 0 5px;
            text-align: center;
            color: #9aa39b;
            font-size: 10px;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes grow {
            from { width: 0; }
        }

        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                padding: 17px;
            }

            .brand { padding: 0 5px 15px; }
            .nav-label { margin-top: 8px; }
            .sidebar nav { display: flex; flex-wrap: wrap; gap: 5px; }
            .nav-link { padding: 10px; margin: 0; font-size: 11px; }
            .sidebar-bottom { margin-top: 15px; }
            .profile-mini { padding-bottom: 10px; }

            .main { margin-left: 0; padding: 20px 15px; }
            .welcome { padding: 26px 22px; min-height: 180px; }
            .welcome-mark { font-size: 65px; padding: 0; }
            .welcome p { font-size: 12px; }
            .platform-pill { display: none; }
            .section-heading h2 { font-size: 20px; }
            .panel { padding: 17px; }
            .campus-actions form { width: 100%; }
            .campus-actions input { flex: 1; width: auto; }
        }

        @media (max-width: 420px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 9px; }
            .stat-card { padding: 14px; }
            .stat-value { font-size: 27px; }
            .stat-label { font-size: 10px; }
            .status-summary { gap: 5px; }
            .status-box { padding: 12px 5px; }
            .status-box strong { font-size: 21px; }
            .welcome-mark { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <a class="brand" href="{{ route('super-admin.dashboard') }}">
            <span class="brand-mark">S</span>
            <span class="brand-name">
                SIPAFIK
                <small>PLATFORM ADMINISTRATION</small>
            </span>
        </a>

        <div class="nav-label">CENTRAL CONTROL</div>
        <nav>
            <a class="nav-link active" href="#overview">
                <span class="nav-icon">◈</span> Overview
            </a>
            <a class="nav-link" href="{{ route('super-admin.users') }}">
                <span class="nav-icon">♙</span> Manajemen Pengguna
            </a>
            <a class="nav-link" href="#analytics">
                <span class="nav-icon">▥</span> User Analytics
            </a>
            <a class="nav-link" href="#campuses">
                <span class="nav-icon">⌂</span> Verifikasi Kampus
            </a>
            <a class="nav-link" href="#recent-users">
                <span class="nav-icon">♙</span> Pengguna
            </a>
            <a class="nav-link" href="{{ route('home') }}">
                <span class="nav-icon">↗</span> Landing Page
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="profile-mini">
                <span class="profile-avatar">S</span>
                <div>
                    <strong>{{ auth()->user()->name ?? 'Super Admin' }}</strong>
                    <small>Administrator pusat</small>
                </div>
            </div>

            <form method="POST" action="{{ route('super-admin.logout') }}">
                @csrf
                <button class="logout-button" type="submit">Keluar dari akun ↗</button>
            </form>
        </div>
    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <span class="eyebrow">CENTRAL ADMINISTRATION</span>
                <p class="date-text">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <span class="platform-pill">SIPAFIK PLATFORM</span>
        </header>

        <section class="welcome" id="overview">
            <div>
                <span class="eyebrow" style="color:#dfc58f">CONTROL CENTER</span>
                <h1>Selamat datang, Super Admin.</h1>
                <p>
                    Pantau perkembangan platform, kelola pengguna,
                    dan verifikasi institusi yang bergabung dengan SIPAFIK.
                </p>
            </div>
            <div class="welcome-mark">S.</div>
        </section>

        <div class="alerts">
            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
        </div>

        <section>
            <div class="section-heading">
                <div>
                    <span class="eyebrow">PLATFORM OVERVIEW</span>
                    <h2>Ringkasan SIPAFIK</h2>
                </div>
                <span class="section-caption">Data terkini dari database</span>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Total Pengguna</span>
                        <span class="stat-icon">♙</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-label">Seluruh akun terdaftar</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Total Kampus</span>
                        <span class="stat-icon">⌂</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['total_campuses']) }}</div>
                    <div class="stat-label">Institusi dalam sistem</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Menunggu Verifikasi</span>
                        <span class="stat-icon">◷</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['pending_campuses']) }}</div>
                    <div class="stat-label">Perlu ditinjau</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Kampus Disetujui</span>
                        <span class="stat-icon">✓</span>
                    </div>
                    <div class="stat-value">{{ number_format($stats['approved_campuses']) }}</div>
                    <div class="stat-label">Institusi terverifikasi</div>
                </article>
            </div>
        </section>

        <section class="content-grid" id="analytics">
            <article class="panel">
                <div class="panel-title">
                    <h3>Distribusi Pengguna</h3>
                    <span>Berdasarkan role akun</span>
                </div>

                @php
                    $maxRoleCount = max(1, (int) ($roleStats->max('total') ?? 1));
                @endphp

                @forelse($roleStats as $role)
                    <div class="role-row">
                        <div class="role-info">
                            <strong>{{ ucwords(str_replace('_', ' ', $role->role)) }}</strong>
                            <span>{{ $role->total }} pengguna</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill"
                                 style="width: {{ ($role->total / $maxRoleCount) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data pengguna.</div>
                @endforelse
            </article>

            <article class="panel">
                <div class="panel-title">
                    <h3>Status Kampus</h3>
                    <span>Ringkasan pendaftaran</span>
                </div>

                <div class="status-summary">
                    <div class="status-box pending">
                        <strong>{{ $stats['pending_campuses'] }}</strong>
                        <span>Pending</span>
                    </div>
                    <div class="status-box approved">
                        <strong>{{ $stats['approved_campuses'] }}</strong>
                        <span>Disetujui</span>
                    </div>
                    <div class="status-box rejected">
                        <strong>{{ $stats['rejected_campuses'] }}</strong>
                        <span>Ditolak</span>
                    </div>
                </div>

                <div style="margin-top:23px">
                    <div class="panel-title">
                        <h3>Informasi Platform</h3>
                    </div>
                    <p style="font-size:11px;line-height:1.8;color:#84918a">
                        Dashboard ini menampilkan data pengguna dan kampus
                        langsung dari database SIPAFIK.
                    </p>
                </div>
            </article>
        </section>

        <section id="campuses">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">CAMPUS ONBOARDING</span>
                    <h2>Verifikasi Kampus</h2>
                </div>
                <span class="section-caption">
                    {{ $campuses->count() }} kampus terdaftar
                </span>
            </div>

            <div class="campus-list">
                @forelse($campuses as $campus)
                    <article class="campus-card">
                        <div class="campus-main">
                            <div class="campus-avatar">
                                {{ strtoupper(substr($campus->name, 0, 1)) }}
                            </div>

                            <div class="campus-info">
                                <h3>{{ $campus->name }}</h3>
                                <p>
                                    {{ '@' . $campus->username }}
                                    · {{ $campus->email }}
                                </p>
                                <small>{{ $campus->address }}</small>
                            </div>
                        </div>

                        <div class="campus-meta">
                            <span class="status status-{{ $campus->status }}">
                                {{ ucfirst($campus->status) }}
                            </span>

                            <span class="contact-meta">
                                Penanggung jawab: {{ $campus->responsible_person }}
                            </span>

                            @if($campus->identity_document)
                                <a class="document-link"
                                   href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($campus->identity_document) }}"
                                   target="_blank"
                                   rel="noopener">
                                    Lihat dokumen ↗
                                </a>
                            @endif
                        </div>

                        @if($campus->status === 'pending')
                            <div class="campus-actions">
                                <form method="POST"
                                      action="{{ route('super-admin.campus.approve', $campus->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="approve-button" type="submit">
                                        Setujui Kampus
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('super-admin.campus.reject', $campus->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text"
                                           name="rejection_reason"
                                           placeholder="Alasan penolakan"
                                           required>
                                    <button class="reject-button" type="submit">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        @elseif($campus->status === 'rejected' && $campus->rejection_reason)
                            <p class="rejection-note">
                                Alasan penolakan: {{ $campus->rejection_reason }}
                            </p>
                        @endif
                    </article>
                @empty
                    <div class="empty-state">
                        <strong>Belum ada pendaftaran kampus</strong>
                        Pendaftaran kampus yang masuk akan muncul di sini.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="recent-users">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">USER MANAGEMENT</span>
                    <h2>Pengguna Terbaru</h2>
                </div>
                <span class="section-caption">6 akun terakhir</span>
            </div>

            <article class="panel">
                <div class="user-list">
                    @forelse($recentUsers as $user)
                        <div class="user-row">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="user-detail">
                                <strong>{{ $user->name }}</strong>
                                <small>{{ $user->email }}</small>
                            </div>
                            <span class="user-role">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada pengguna.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <footer class="footer">
            SIPAFIK · Satu ruang untuk setiap kegiatan kampus.
        </footer>

    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.sidebar .nav-link');

    function updateActiveMenu() {
        const currentHash = window.location.hash || '#overview';

        navLinks.forEach(link => {
            const target = link.getAttribute('href');

            link.classList.toggle(
                'active',
                target === currentHash
            );
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            navLinks.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    window.addEventListener('hashchange', updateActiveMenu);

    updateActiveMenu();
});
</script>
</body>
</html>
