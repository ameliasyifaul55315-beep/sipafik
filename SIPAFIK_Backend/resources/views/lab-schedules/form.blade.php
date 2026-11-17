<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($schedule) ? 'Edit' : 'Tambah' }} Jadwal — SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <style>
        .schedule-form { max-width:760px; margin:auto; padding:26px; background:#fff; border-radius:18px; }
        .field { margin-bottom:17px; }
        .field label { display:block; margin-bottom:7px; font-weight:600; color:#35483b; }
        .field input,.field select,.field textarea { width:100%; box-sizing:border-box; padding:12px; border:1px solid #dddcd3; border-radius:10px; font:inherit; background:#fffefa; }
        .field textarea { min-height:80px; }
        .actions { display:flex; gap:10px; flex-wrap:wrap; }
        .schedule-button { padding:11px 17px; background:#315b45; color:white; border:0; border-radius:10px; cursor:pointer; text-decoration:none; font:inherit; }
        .schedule-button.secondary { background:#e9e4d9; color:#35483b; }
        .errors { color:#a0443c; margin-bottom:15px; }
    </style>
</head>
<body class="dashboard-page">
<aside class="dash-sidebar">
    <a class="auth-brand" href="{{ route('dashboard') }}"><span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS PLATFORM</small></span></a>
    <div class="sidebar-label">WORKSPACE</div>
    <a class="side-link" href="{{ route('dashboard') }}">Dashboard</a>
    <a class="side-link" href="{{ route('campus.laboratories.index') }}">Laboratorium</a>
    <a class="side-link active" href="{{ route('campus.lab-schedules.index') }}">Jadwal Laboratorium</a>
    <div class="sidebar-bottom">
        <div class="user-mini"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}</small></div></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" type="submit">Keluar dari akun</button></form>
    </div>
</aside>

<main class="dash-main">
    <header class="dash-top">
        <div><span class="eyebrow">MANAJEMEN FASILITAS</span><p>{{ now()->translatedFormat('l, d F Y') }}</p></div>
        <span class="campus-pill">{{ auth()->user()->campus?->name }}</span>
    </header>
    <section class="dash-welcome">
        <div><span class="eyebrow">AGENDA KAMPUS</span><h1>{{ isset($schedule) ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h1><p>Lengkapi informasi penggunaan laboratorium.</p></div>
        <div class="welcome-mark">S.</div>
    </section>

    <div class="schedule-form">
        @if($errors->any())
            <div class="errors"><strong>Periksa kembali data berikut:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        @if($laboratories->isEmpty())
            <p>Belum ada laboratorium aktif. Tambahkan atau aktifkan laboratorium terlebih dahulu.</p>
        @elseif($lecturers->isEmpty())
            <p>Belum ada akun dosen aktif di kampus ini. Tambahkan akun dosen terlebih dahulu.</p>
        @else
            <form method="POST" action="{{ isset($schedule) ? route('campus.lab-schedules.update', $schedule->id) : route('campus.lab-schedules.store') }}">
                @csrf
                @if(isset($schedule)) @method('PUT') @endif

                <div class="field">
                    <label for="laboratory_id">Laboratorium</label>
                    <select name="laboratory_id" id="laboratory_id" required>
                        <option value="">Pilih laboratorium</option>
                        @foreach($laboratories as $lab)
                            <option value="{{ $lab->id }}" @selected((string)old('laboratory_id', $schedule->laboratory_id ?? '') === (string)$lab->id)>{{ $lab->name }} — {{ $lab->location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="lecturer_id">Dosen Penanggung Jawab</label>
                    <select name="lecturer_id" id="lecturer_id" required>
                        <option value="">Pilih dosen</option>
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((string)old('lecturer_id', $schedule->lecturer_id ?? '') === (string)$lecturer->id)>{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="course_name">Nama Mata Kuliah / Kegiatan</label>
                    <input name="course_name" id="course_name" value="{{ old('course_name', $schedule->course_name ?? '') }}" required>
                </div>

                <div class="field">
                    <label for="class_name">Kelas</label>
                    <input name="class_name" id="class_name" value="{{ old('class_name', $schedule->class_name ?? '') }}" required>
                </div>

                <div class="field">
                    <label for="schedule_date">Tanggal</label>
                    <input type="date" name="schedule_date" id="schedule_date" value="{{ old('schedule_date', isset($schedule) ? $schedule->schedule_date->format('Y-m-d') : '') }}" required>
                </div>

                <div class="field">
                    <label for="start_time">Waktu Mulai</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', isset($schedule) ? substr($schedule->start_time, 0, 5) : '') }}" required>
                </div>

                <div class="field">
                    <label for="end_time">Waktu Selesai</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', isset($schedule) ? substr($schedule->end_time, 0, 5) : '') }}" required>
                </div>

                <div class="field">
                    <label for="schedule_type">Jenis Jadwal</label>
                    <select name="schedule_type" id="schedule_type" required>
                        <option value="">Pilih jenis</option>
                        @foreach(['praktikum', 'perkuliahan', 'kegiatan', 'lainnya'] as $type)
                            <option value="{{ $type }}" @selected(old('schedule_type', $schedule->schedule_type ?? '') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                @if(isset($schedule))
                    <div class="field">
                        <label for="status">Status</label>
                        <select name="status" id="status" required>
                            @foreach(['scheduled', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $schedule->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="field">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes">{{ old('notes', $schedule->notes ?? '') }}</textarea>
                </div>

                <div class="actions">
                    <button class="schedule-button" type="submit">{{ isset($schedule) ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</button>
                    <a class="schedule-button secondary" href="{{ route('campus.lab-schedules.index') }}">Kembali</a>
                </div>
            </form>
        @endif
    </div>
</main>
</body>
</html>
