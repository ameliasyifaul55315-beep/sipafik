<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($laboratory) ? 'Edit' : 'Tambah' }} Laboratorium — SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <style>
        .lab-form-wrap { max-width:760px; margin:0 auto; background:white; padding:28px; border-radius:18px; }
        .lab-field { margin-bottom:18px; }
        .lab-field label { display:block; margin-bottom:7px; font-weight:600; color:#35483b; }
        .lab-field input,.lab-field textarea,.lab-field select { width:100%; box-sizing:border-box; padding:12px 14px; border:1px solid #dddcd3; border-radius:10px; font:inherit; background:#fffefa; }
        .lab-field textarea { min-height:95px; resize:vertical; }
        .lab-button { display:inline-block; padding:11px 18px; border:0; border-radius:10px; background:#315b45; color:white; text-decoration:none; cursor:pointer; font:inherit; }
        .lab-button.secondary { background:#e9e4d9; color:#35483b; }
        .lab-error { color:#a0443c; font-size:13px; margin-top:5px; }
        .lab-actions { display:flex; gap:10px; flex-wrap:wrap; }
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
    <div class="sidebar-bottom">
        <div class="user-mini">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div><strong>{{ auth()->user()->name }}</strong><small>Admin Kampus</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-button" type="submit">Keluar dari akun</button>
        </form>
    </div>
</aside>

<main class="dash-main">
    <header class="dash-top">
        <div><span class="eyebrow">MANAJEMEN FASILITAS</span><p>{{ now()->translatedFormat('l, d F Y') }}</p></div>
        <span class="campus-pill">{{ auth()->user()->campus?->name }}</span>
    </header>

    <section class="dash-welcome">
        <div>
            <span class="eyebrow">DATA FASILITAS KAMPUS</span>
            <h1>{{ isset($laboratory) ? 'Edit Laboratorium' : 'Tambah Laboratorium' }}</h1>
            <p>Lengkapi informasi laboratorium kampus dengan benar.</p>
        </div>
        <div class="welcome-mark">S.</div>
    </section>

    <div class="lab-form-wrap">
        @if($errors->any())
            <div class="lab-error">
                <strong>Periksa kembali isian berikut:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ isset($laboratory) ? route('campus.laboratories.update', $laboratory->id) : route('campus.laboratories.store') }}">
            @csrf
            @if(isset($laboratory))
                @method('PUT')
            @endif

            <div class="lab-field">
                <label for="code">Kode Laboratorium</label>
                <input id="code" name="code" value="{{ old('code', $laboratory->code ?? '') }}" required maxlength="50" placeholder="Contoh: LAB-001">
            </div>

            <div class="lab-field">
                <label for="name">Nama Laboratorium</label>
                <input id="name" name="name" value="{{ old('name', $laboratory->name ?? '') }}" required placeholder="Contoh: Laboratorium Komputer">
            </div>

            <div class="lab-field">
                <label for="location">Lokasi</label>
                <input id="location" name="location" value="{{ old('location', $laboratory->location ?? '') }}" required placeholder="Contoh: Gedung Fakultas Teknik">
            </div>

            <div class="lab-field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" placeholder="Keterangan penggunaan laboratorium">{{ old('description', $laboratory->description ?? '') }}</textarea>
            </div>

            <div class="lab-field">
                <label for="capacity">Kapasitas (orang)</label>
                <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $laboratory->capacity ?? 30) }}" required>
            </div>

            <div class="lab-field">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" name="facilities" placeholder="Pisahkan dengan koma, contoh: Komputer, Proyektor, AC, WiFi">{{ old('facilities', isset($laboratory) ? implode(', ', $laboratory->facilities ?? []) : '') }}</textarea>
            </div>

            <div class="lab-field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active" @selected(old('status', $laboratory->status ?? 'active') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $laboratory->status ?? '') === 'inactive')>Nonaktif</option>
                </select>
            </div>

            <div class="lab-actions">
                <button class="lab-button" type="submit">{{ isset($laboratory) ? 'Simpan Perubahan' : 'Simpan Laboratorium' }}</button>
                <a class="lab-button secondary" href="{{ route('campus.laboratories.index') }}">Kembali</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>
