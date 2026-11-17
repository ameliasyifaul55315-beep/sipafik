<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Laboratorium — SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <style>
        .schedule-wrap { max-width:1100px; margin:auto; }
        .schedule-panel { background:#fff; padding:20px; border-radius:16px; overflow-x:auto; }
        .schedule-table { width:100%; border-collapse:collapse; min-width:750px; }
        .schedule-table th,.schedule-table td { padding:13px; text-align:left; border-bottom:1px solid #eeeae2; }
        .schedule-table th { font-size:12px; color:#68766b; text-transform:uppercase; }
        .schedule-button { display:inline-block; padding:10px 15px; background:#315b45; color:white; border-radius:9px; text-decoration:none; border:0; cursor:pointer; }
        .schedule-button.secondary { background:#e9e4d9; color:#35483b; }
        .schedule-alert { padding:12px 16px; background:#e5f1e8; color:#315b45; border-radius:10px; margin-bottom:15px; }
        .schedule-status { padding:5px 10px; border-radius:20px; background:#e5f1e8; color:#315b45; font-size:12px; }
    </style>
</head>
<body class="dashboard-page">
<aside class="dash-sidebar">
    <a class="auth-brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS PLATFORM</small></span>
    </a>
    <div class="sidebar-label">WORKSPACE</div>
    <a class="side-link" href="{{ route('dashboard') }}">Dashboard</a>
    <a class="side-link" href="{{ route('campus.laboratories.index') }}">Laboratorium</a>
    <a class="side-link active" href="{{ route('campus.lab-schedules.index') }}">Jadwal Laboratorium</a>
    <div class="sidebar-bottom">
        <div class="user-mini">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div><strong>{{ auth()->user()->name }}</strong><small>{{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="logout-button" type="submit">Keluar dari akun</button>
        </form>
    </div>
</aside>

<main class="dash-main">
    <div class="schedule-wrap">
        <header class="dash-top">
            <div><span class="eyebrow">MANAJEMEN FASILITAS</span><p>{{ now()->translatedFormat('l, d F Y') }}</p></div>
            <span class="campus-pill">{{ auth()->user()->campus?->name }}</span>
        </header>

        <section class="dash-welcome">
            <div>
                <span class="eyebrow">PENGATURAN RUANG</span>
                <h1>Jadwal Laboratorium</h1>
                <p>Pantau jadwal penggunaan laboratorium kampus.</p>
            </div>
            <div class="welcome-mark">S.</div>
        </section>

        @if(session('success'))
            <div class="schedule-alert">{{ session('success') }}</div>
        @endif

        <section class="dash-section-head">
            <div><span class="eyebrow">AGENDA KAMPUS</span><h2>Daftar jadwal</h2></div>
            @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas']))
                <a class="schedule-button" href="{{ route('campus.lab-schedules.create') }}">+ Tambah Jadwal</a>
            @endif
        </section>

        <div class="schedule-panel">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Laboratorium</th><th>Mata Kuliah</th>
                        <th>Kelas</th><th>Dosen</th><th>Waktu</th><th>Status</th>
                        @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas']))
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->schedule_date?->format('d/m/Y') }}</td>
                            <td>{{ $schedule->laboratory?->name ?? 'Laboratorium tidak tersedia' }}</td>
                            <td>{{ $schedule->course_name }}</td>
                            <td>{{ $schedule->class_name }}</td>
                            <td>{{ $schedule->lecturer?->name ?? '—' }}</td>
                            <td>{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</td>
                            <td><span class="schedule-status">{{ ucfirst($schedule->status) }}</span></td>
                            @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas']))
                                <td>
                                    <a class="schedule-button secondary" href="{{ route('campus.lab-schedules.edit', $schedule->id) }}">Edit</a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="8">Belum ada jadwal laboratorium untuk kampus ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top:18px">{{ $schedules->links() }}</div>
        </div>
        <footer class="dash-footer">SIPAFIK · Satu ruang untuk setiap kegiatan kampus.</footer>
    </div>
</main>
</body>
</html>
