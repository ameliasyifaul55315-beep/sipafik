@extends('layouts.dashboard')

@section('title', $content['title'])
@section('eyebrow', 'SIPAFIK WORKSPACE')
@section('kicker', 'SELAMAT DATANG, '.strtoupper(\Illuminate\Support\Str::of($user->name)->explode(' ')->first()))
@section('heading', $content['title'])
@section('subheading', $content['subtitle'])

@section('content')
    <div class="mod-stats">
        @foreach($stats as [$label, $value, $note])
            <div class="mod-stat"><span>{{ $label }}</span><strong>{{ $value }}</strong><small>{{ $note }}</small></div>
        @endforeach
    </div>

    <section class="dash-section-head">
        <div><span class="eyebrow">MENU UTAMA</span><h2>Ruang kerja kamu</h2></div>
        <span class="section-caption">Pilih area untuk mulai bekerja</span>
    </section>

    <section class="dash-grid">
        @foreach($content['items'] as $item)
            <article class="dash-card">
                <span class="dash-number">{{ $item['number'] }}</span>
                <div class="dash-card-icon">✳</div>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['description'] }}</p>
                <a href="{{ $item['url'] }}" class="mod-btn sm" style="margin-top:12px">Buka Modul →</a>
            </article>
        @endforeach
    </section>

    <div class="mod-panel" style="margin-top:34px">
        <h3>{{ $panel['title'] }}</h3>
        @if(empty($panel['rows']))
            <div class="mod-empty">{{ $panel['empty'] }}</div>
        @else
            <div class="mod-timeline">
                @foreach($panel['rows'] as [$title, $sub, $badge, $badgeLabel, $url])
                    <div class="mod-item">
                        <div><strong>{{ $title }}</strong><small>{{ $sub }}</small></div>
                        <div style="display:flex;gap:8px;align-items:center">
                            <span class="mod-badge {{ $badge }}">{{ $badgeLabel }}</span>
                            <a class="mod-btn ghost sm" href="{{ $url }}">Buka</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
