@php
    $u = auth()->user();

    $menus = [
        'admin_kampus' => [
            ['Pengguna Kampus', 'campus.users.index', 'campus.users.*'],
            ['Kegiatan', 'campus.events.index', 'campus.events.index|campus.events.create|campus.events.edit|campus.events.show'],
            ['Jadwal Kegiatan', 'campus.events.schedule', 'campus.events.schedule'],
            ['Pendaftar Kegiatan', 'campus.registrations.index', 'campus.registrations.*'],
            ['Laboratorium', 'campus.laboratories.index', 'campus.laboratories.*'],
            ['Jadwal Laboratorium', 'campus.lab-schedules.index', 'campus.lab-schedules.*'],
            ['Permohonan Fasilitas', 'campus.facility-requests.index', 'campus.facility-requests.*'],
        ],
        'organisasi' => [
            ['Kegiatan Organisasi', 'campus.events.index', 'campus.events.index|campus.events.create|campus.events.edit|campus.events.show'],
            ['Pendaftar Kegiatan', 'campus.registrations.index', 'campus.registrations.*'],
            ['Jadwal Kegiatan', 'campus.events.schedule', 'campus.events.schedule'],
            ['Permohonan Fasilitas', 'campus.facility-requests.index', 'campus.facility-requests.*'],
        ],
        'dosen' => [
            ['Kegiatan Saya', 'campus.events.index', 'campus.events.index|campus.events.create|campus.events.edit|campus.events.show'],
            ['Peserta Kegiatan', 'campus.registrations.index', 'campus.registrations.*'],
            ['Jadwal Laboratorium', 'campus.lab-schedules.index', 'campus.lab-schedules.*'],
            ['Permohonan Fasilitas', 'campus.facility-requests.index', 'campus.facility-requests.*'],
        ],
        'petugas_fasilitas' => [
            ['Data Laboratorium', 'campus.laboratories.index', 'campus.laboratories.*'],
            ['Permohonan Masuk', 'campus.facility-requests.index', 'campus.facility-requests.*'],
            ['Jadwal Penggunaan', 'campus.lab-schedules.index', 'campus.lab-schedules.*'],
        ],
        'mahasiswa' => [
            ['Jelajahi Kegiatan', 'campus.events.index', 'campus.events.index|campus.events.show'],
            ['Pendaftaran Saya', 'campus.registrations.index', 'campus.registrations.*'],
            ['Jadwal Kegiatan', 'campus.events.schedule', 'campus.events.schedule'],
            ['Permohonan Fasilitas', 'campus.facility-requests.index', 'campus.facility-requests.*'],
        ],
    ];

    $items = $menus[$u->role] ?? [];
    $isActive = function (string $patterns) {
        foreach (explode('|', $patterns) as $p) {
            if (request()->routeIs($p)) {
                return true;
            }
        }
        return false;
    };
@endphp
<aside class="dash-sidebar">
    <a class="auth-brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS PLATFORM</small></span>
    </a>

    <div class="sidebar-label">WORKSPACE</div>
    <nav aria-label="Menu utama">
        <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
        @foreach($items as [$label, $routeName, $pattern])
            <a class="side-link {{ $isActive($pattern) ? 'active' : '' }}" href="{{ route($routeName) }}">{{ $label }}</a>
        @endforeach
        <a class="side-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">Profil &amp; Kampus</a>
        <a class="side-link" href="{{ route('home') }}">Lihat Landing Page</a>
    </nav>

    <div class="sidebar-bottom">
        <div class="user-mini">
            <span class="avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
            <div>
                <a class="profile-link" href="{{ route('profile.show') }}"><strong>{{ $u->name }}</strong></a>
                <small>{{ $u->role_label }}</small>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-button" type="submit">Keluar dari akun ↗</button>
        </form>
    </div>
</aside>
