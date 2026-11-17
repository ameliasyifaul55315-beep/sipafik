<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — SIPAFIK</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('sipafik/css/modules.css') }}">
</head>
<body>
<div class="notice-wrap">
    <div class="notice-card">
        <p class="code">S.</p>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <p style="font-size:12px">Masuk sebagai <strong>{{ $user->email }}</strong> ({{ $user->role_label }})</p>
        <div class="mod-actions">
            <a class="mod-btn ghost" href="{{ route('home') }}">Ke Beranda</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="mod-btn" type="submit">Keluar dari akun</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
