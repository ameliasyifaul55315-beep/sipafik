@extends('layouts.dashboard')

@php
    $u = auth()->user();
    $isStudent = $u->role === 'mahasiswa';
@endphp

@section('title', $isStudent ? 'Jelajahi Kegiatan' : 'Kegiatan')
@section('eyebrow', 'KEGIATAN KAMPUS')
@section('kicker', $isStudent ? 'TEMUKAN & DAFTAR' : 'PENYELENGGARAAN KEGIATAN')
@section('heading', $isStudent ? 'Jelajahi Kegiatan' : 'Kegiatan')
@section('subheading', $isStudent
    ? 'Daftar kegiatan kampusmu dan kegiatan publik dari kampus lain yang dibuka untuk umum.'
    : 'Buat, ubah, dan pantau kegiatan beserta kuota pesertanya.')

@section('actions')
    @if($isManager)
        <a class="mod-btn" href="{{ route('campus.events.create') }}">+ Buat Kegiatan</a>
    @endif
@endsection

@section('content')
    @php
        $tabs = $isStudent || $u->role === 'admin_kampus'
            ? ['campus' => 'Kampus Saya', 'public' => 'Kampus Lain (Publik)']
            : ['mine' => 'Kegiatan Saya', 'campus' => 'Semua Kegiatan Kampus', 'public' => 'Kampus Lain (Publik)'];
    @endphp
    <div class="mod-tabs">
        @foreach($tabs as $key => $label)
            <a class="{{ $scope === $key ? 'active' : '' }}" href="{{ route('campus.events.index', ['scope' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    <form class="mod-filter" method="GET" action="{{ route('campus.events.index') }}">
        <input type="hidden" name="scope" value="{{ $scope }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul atau lokasi…">
        <select name="category">
            <option value="">Semua kategori</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="time">
            <option value="upcoming" @selected(request('time') !== 'past')>Akan datang</option>
            <option value="past" @selected(request('time') === 'past')>Sudah berlalu</option>
        </select>
        <button class="mod-btn ghost sm" type="submit">Terapkan</button>
    </form>

    @if($events->isEmpty())
        <div class="mod-panel"><div class="mod-empty"><strong>Belum ada kegiatan</strong>Tidak ada kegiatan yang cocok dengan filter ini.</div></div>
    @else
        <div class="mod-cards">
            @foreach($events as $event)
                @php
                    $used = $event->active_registrations_count;
                    $percent = $event->quota > 0 ? min(100, round($used / $event->quota * 100)) : 0;
                    $registered = in_array($event->id, $myRegistrations, true);
                @endphp
                <article class="mod-card">
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="mod-badge gold">{{ $event->category_label }}</span>
                        <span class="mod-badge {{ $event->status }}">{{ $event->status_label }}</span>
                        @if($event->visibility === 'public')<span class="mod-badge info">Publik</span>@endif
                        @if($registered)<span class="mod-badge registered">Terdaftar</span>@endif
                    </div>
                    <h3>{{ $event->title }}</h3>
                    <div class="meta">
                        <span>📅 {{ $event->event_date->translatedFormat('l, d F Y') }} · {{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }}</span>
                        <span>📍 {{ $event->location }}</span>
                        <span>🏛 {{ $event->campus?->name }}</span>
                    </div>
                    <div>
                        <div class="mod-quota"><i style="width:{{ $percent }}%"></i></div>
                        <small class="muted">{{ $used }} / {{ $event->quota }} peserta</small>
                    </div>
                    <div class="foot">
                        <a class="mod-btn sm" href="{{ route('campus.events.show', $event->id) }}">Lihat Detail</a>
                    </div>
                </article>
            @endforeach
        </div>
        {{ $events->links() }}
    @endif
@endsection
