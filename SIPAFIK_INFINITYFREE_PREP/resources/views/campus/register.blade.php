<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kampus — SIPAFIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
</head>
<body class="auth-page register-page">
<div class="auth-shell wide-shell">
    <a class="auth-brand" href="{{ route('home') }}"><span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS EVENTS & FACILITIES</small></span></a>
    <a class="back-link" href="{{ route('home') }}">← Kembali ke beranda</a>
    <main class="register-card">
        <div class="register-intro"><span class="eyebrow">JOIN THE CAMPUS NETWORK</span><h1>Daftarkan <em>kampusmu.</em></h1><p>Lengkapi informasi institusi untuk bergabung dengan platform SIPAFIK. Pendaftaran akan diperiksa oleh Super Admin sebelum akun kampus diaktifkan.</p><div class="register-note"><span>✳</span><div><strong>Proses verifikasi</strong><small>Siapkan dokumen identitas resmi kampus dalam format PDF, JPG, atau PNG (maksimal 2 MB).</small></div></div></div>
        <section class="register-form-wrap">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('campus.register.store') }}" enctype="multipart/form-data" class="register-form">
                @csrf
                <div class="form-section-title"><span>01</span><div><strong>Informasi kampus</strong><small>Identitas resmi institusi</small></div></div>
                <div class="field-grid">
                    <div class="field full"><label for="name">Nama Kampus *</label><input id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Universitas ..." required></div>
                    <div class="field"><label for="username">Username Kampus *</label><input id="username" name="username" value="{{ old('username') }}" placeholder="nama-kampus" pattern="[A-Za-z0-9_-]+" required><small>Huruf kecil, angka, tanda hubung, atau garis bawah.</small></div>
                    <div class="field"><label for="email">Email Resmi Kampus *</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@kampus.ac.id" required></div>
                    <div class="field"><label for="phone">Nomor Kontak</label><input id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"></div>
                    <div class="field"><label for="responsible_person">Nama Penanggung Jawab *</label><input id="responsible_person" name="responsible_person" value="{{ old('responsible_person') }}" placeholder="Nama lengkap" required></div>
                    <div class="field full"><label for="address">Alamat Kampus *</label><textarea id="address" name="address" rows="3" placeholder="Alamat lengkap kampus" required>{{ old('address') }}</textarea></div>
                    <div class="field full"><label for="identity_document">Dokumen Identitas Kampus *</label><input id="identity_document" type="file" name="identity_document" accept=".pdf,.jpg,.jpeg,.png" required><small>PDF/JPG/PNG · Maksimal 2 MB · Gunakan dokumen resmi untuk verifikasi.</small></div>
                </div>
                <div class="form-section-title second-section"><span>02</span><div><strong>Akun admin kampus</strong><small>Digunakan setelah pendaftaran disetujui</small></div></div>
                <div class="field-grid">
                    <div class="field"><label for="password">Password Awal *</label><input id="password" type="password" name="password" placeholder="Minimal 8 karakter" minlength="8" required></div>
                    <div class="field"><label for="password_confirmation">Konfirmasi Password *</label><input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" minlength="8" required></div>
                </div>
                <button class="primary-button" type="submit">Kirim Pendaftaran <span>↗</span></button>
                <p class="privacy-note">Dengan mengirim formulir ini, data kampus akan diproses untuk keperluan verifikasi SIPAFIK.</p>
            </form>
        </section>
    </main>
    <footer class="auth-footer">Sudah memiliki akun? <a href="{{ route('login') }}">Login di sini</a></footer>
</div></body></html>
