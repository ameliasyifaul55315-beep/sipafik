<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Fasilitas | SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        body { background:#f5f3eb; color:#26352d; }
        .container { max-width:720px; margin:40px auto; padding:20px; }
        .panel { background:white; padding:30px; border-radius:16px; box-shadow:0 5px 24px #26352d12; }
        h1 { color:#244b38; }
        label { display:block; font-weight:600; margin:15px 0 6px; }
        input,select,textarea { width:100%; padding:11px; border:1px solid #d5d8cf; border-radius:8px; box-sizing:border-box; }
        textarea { min-height:100px; }
        .btn { display:inline-block; padding:11px 18px; background:#315b43; color:white; border:0; border-radius:8px; text-decoration:none; cursor:pointer; margin-top:20px; }
        .secondary { background:#777; }
        .error { color:#a32d2d; font-size:13px; }
        .notice { background:#f0f1e9; padding:13px; border-radius:8px; margin:15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="panel">
        <h1>Ajukan Penggunaan Fasilitas</h1>
        <p>Lengkapi informasi berikut untuk mengajukan penggunaan fasilitas kampus.</p>

        @if($errors->any())
            <div class="notice">
                @foreach($errors->all() as $error)
                    <p class="error">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if($laboratories->isEmpty())
            <div class="notice">
                Belum tersedia laboratorium aktif di kampus Anda. Silakan hubungi Admin Kampus atau Petugas Fasilitas.
            </div>
        @else
            <form method="POST" action="{{ route('campus.facility-requests.store') }}">
                @csrf

                <label>Fasilitas / Laboratorium</label>
                <select name="laboratory_id" required>
                    <option value="">-- Pilih fasilitas --</option>
                    @foreach($laboratories as $laboratory)
                        <option value="{{ $laboratory->id }}" @selected(old('laboratory_id') == $laboratory->id)>
                            {{ $laboratory->name }} — Kapasitas {{ $laboratory->capacity }} orang
                        </option>
                    @endforeach
                </select>

                <label>Judul Kegiatan</label>
                <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required>

                <label>Tujuan / Keperluan</label>
                <textarea name="purpose" required>{{ old('purpose') }}</textarea>

                <label>Tanggal Penggunaan</label>
                <input type="date" name="request_date" value="{{ old('request_date') }}" min="{{ date('Y-m-d') }}" required>

                <label>Waktu Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" required>

                <label>Waktu Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" required>

                <label>Jumlah Peserta</label>
                <input type="number" name="participants_count" min="1" value="{{ old('participants_count', 1) }}" required>

                <div class="notice">
                    Permohonan akan berstatus <strong>Pending</strong> sampai diperiksa oleh Admin Kampus atau Petugas Fasilitas.
                </div>

                <button type="submit" class="btn">Kirim Permohonan</button>
                <a href="{{ route('campus.facility-requests.index') }}" class="btn secondary">Kembali</a>
            </form>
        @endif
    </div>
</div>
</body>
</html>
