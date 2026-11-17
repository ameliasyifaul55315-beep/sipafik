<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIPAFIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
</head>
<body class="auth-page">
<div class="auth-shell">
    <a class="auth-brand" href="{{ route('home') }}"><span class="brand-mark">S</span><span>SIPAFIK<small>CAMPUS EVENTS & FACILITIES</small></span></a>
    <a class="back-link" href="{{ route('home') }}">← Kembali ke beranda</a>
    <main class="auth-card login-card">
        <section class="auth-story"><span class="eyebrow">YOUR CAMPUS, CONNECTED</span><h1>Selamat datang<br><em>kembali.</em></h1><p>Satu ruang untuk mengelola kegiatan, menemukan aktivitas kampus, dan menggunakan fasilitas dengan lebih terarah.</p><div class="story-foot"><span>01</span><span>CAMPUS EVENTS & FACILITIES PLATFORM</span></div></section>
        <section class="auth-form-wrap">
            <div class="form-heading"><span class="eyebrow">AKSES PLATFORM</span><h2>Masuk ke akun</h2><p>Gunakan email dan password akun SIPAFIK kamu.</p></div>
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('login.process') }}" class="auth-form">
                @csrf
                <label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="username" required autofocus>
                <div class="label-row"><label for="password">Password</label></div><input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                <label class="remember"><input type="checkbox" name="remember" value="1"> <span>Ingat saya</span></label>
                <button class="primary-button" type="submit">Masuk ke SIPAFIK <span>↗</span></button>
            </form>
            <div class="form-bottom">Kampus belum terdaftar? <a href="{{ route('campus.register') }}">Daftarkan kampus</a></div>
            <div class="super-link"><a href="{{ route('super-admin.login') }}">Login khusus Super Admin</a></div>
        </section>
    </main>
    <footer class="auth-footer">© {{ date('Y') }} SIPAFIK · Satu ruang untuk setiap kegiatan kampus.</footer>
</div>
</body></html>
