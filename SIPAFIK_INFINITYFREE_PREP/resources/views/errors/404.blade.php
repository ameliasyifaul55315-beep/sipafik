<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman tidak ditemukan | SIPAFIK</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('sipafik/css/modules.css') }}">
</head>
<body>
<div class="notice-wrap">
    <div class="notice-card">
        <p class="code">404</p>
        <h1>Halaman tidak ditemukan</h1>
        <p>Halaman yang Anda cari tidak ada, sudah dipindahkan, atau bukan milik kampus Anda.</p>
        <div class="mod-actions">
            <a class="mod-btn ghost" href="{{ url('/') }}">Ke Beranda</a>
            @auth
                <a class="mod-btn" href="{{ route('dashboard') }}">Ke Dashboard</a>
            @else
                <a class="mod-btn" href="{{ route('login') }}">Masuk</a>
            @endauth
        </div>
    </div>
</div>
</body>
</html>
