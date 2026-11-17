<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium — SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <style>
        .lab-content { max-width: 1100px; margin: 0 auto; }
        .lab-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .lab-button { display:inline-block; padding:10px 16px; border:0; border-radius:10px; background:#315b45; color:white; text-decoration:none; cursor:pointer; font:inherit; }
        .lab-button.secondary { background:#e9e4d9; color:#35483b; }
        .lab-table-wrap { overflow-x:auto; background:white; border-radius:16px; padding:16px; }
        .lab-table { width:100%; border-collapse:collapse; min-width:650px; }
        .lab-table th,.lab-table td { padding:13px 12px; text-align:left; border-bottom:1px solid #eeeae2; }
        .lab-table th { color:#68766b; font-size:12px; text-transform:uppercase; }
        .lab-status { padding:5px 10px; border-radius:20px; font-size:12px; background:#e5f1e8; color:#315b45; }
        .lab-status.off { background:#f5e8e5; color:#94594e; }
        .lab-alert { padding:12px 16px; border-radius:10px; margin-bottom:16px; background:#e5f1e8; color:#315b45; }
        .lab-pagination { margin-top:18px; }
    </style>
</head>
<body class="dashboard-page">
<aside class="dash-sidebar">
    <a class="auth-brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS PLATFORM</small></span>
    </a>
    <div class="sidebar-label">WORKSPACE</div>
    <a class="side-link" href="{{ route('dashboard') }}">Dashboard</a>
    <a class="side-link active" href="{{ route('campus.laboratories.index') }}">Laboratorium</a>
    <a class="side-link" href="{{ route('home') }}">Lihat Landing Page</a>
    <div class="sidebar-bottom">
        <div class="user-mini">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}</small>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-button" type="submit">Keluar dari akun</button>
        </form>
    </div>
</aside>

<main class="dash-main">
    <div class="lab-content">
        <header class="dash-top">
            <div>
                <span class="eyebrow">MANAJEMEN FASILITAS</span>
                <p>{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <span class="campus-pill">{{ auth()->user()->campus?->name }}</span>
        </header>

        <section class="dash-welcome">
            <div>
                <span class="eyebrow">DATA FASILITAS KAMPUS</span>
                <h1>Laboratorium</h1>
                <p>Kelola informasi ruang laboratorium dan fasilitas pendukung kampus.</p>
            </div>
            <div class="welcome-mark">S.</div>
        </section>

        @if(session('success'))
            <div class="lab-alert">{{ session('success') }}</div>
        @endif

        <section class="dash-section-head">
            <div>
                <span class="eyebrow">DAFTAR FASILITAS</span>
                <h2>Data laboratorium</h2>
            </div>
            @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas'], true))
                <a class="lab-button" href="{{ route('campus.laboratories.create') }}">+ Tambah Laboratorium</a>
            @endif
        </section>

        <div class="lab-table-wrap">
            <table class="lab-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Laboratorium</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas'], true))
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($laboratories as $lab)
                        <tr>
                            <td>{{ $lab->code }}</td>
                            <td>
                                <strong>{{ $lab->name }}</strong>
                                <br><small>{{ implode(', ', $lab->facilities ?? []) }}</small>
                            </td>
                            <td>{{ $lab->location }}</td>
                            <td>{{ $lab->capacity }} orang</td>
                            <td>
                                <span class="lab-status {{ $lab->status === 'active' ? '' : 'off' }}">
                                    {{ $lab->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas'], true))
                                <td>
                                    <div class="lab-actions">
                                        <a class="lab-button secondary" href="{{ route('campus.laboratories.edit', $lab->id) }}">Edit</a>
                                        <form method="POST" action="{{ route('campus.laboratories.toggle-status', $lab->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="lab-button secondary" type="submit">
                                                {{ $lab->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas'], true) ? 6 : 5 }}">
                                Belum ada data laboratorium untuk kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="lab-pagination">{{ $laboratories->links() }}</div>
        </div>

        <footer class="dash-footer">SIPAFIK · Satu ruang untuk setiap kegiatan kampus.</footer>
    </div>
</main>
</body>
</html>
