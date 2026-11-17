<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — SIPAFIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sipafik/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('sipafik/css/modules.css') }}">
    @stack('head')
</head>
<body class="dashboard-page">
    @include('partials.sidebar')

    <main class="dash-main">
        <header class="dash-top">
            <div>
                <span class="eyebrow">@yield('eyebrow', 'SIPAFIK WORKSPACE')</span>
                <p>{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <span class="campus-pill">{{ auth()->user()->campus?->name ?? 'Platform SIPAFIK' }}</span>
        </header>

        <section class="mod-head">
            <div>
                <span class="eyebrow">@yield('kicker')</span>
                <h1>@yield('heading')</h1>
                @hasSection('subheading')
                    <p>@yield('subheading')</p>
                @endif
            </div>
            @hasSection('actions')
                <div class="mod-actions">@yield('actions')</div>
            @endif
        </section>

        @if(session('success'))
            <div class="mod-alert" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mod-alert error" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mod-alert error" role="alert">
                <strong>Periksa kembali isian Anda:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

        <footer class="dash-footer">SIPAFIK · Satu ruang untuk setiap kegiatan kampus.</footer>
    </main>
</body>
</html>
