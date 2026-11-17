@extends('layouts.dashboard')

@php
    $u = auth()->user();
    $used = $event->active_registrations_count;
    $full = $used >= $event->quota;
    $past = $event->event_date->isBefore(today());
    $isActiveReg = $myRegistration && in_array($myRegistration->status, ['registered', 'attended'], true);
@endphp

@section('title', $event->title)
@section('eyebrow', 'KEGIATAN KAMPUS')
@section('kicker', strtoupper($event->category_label))
@section('heading', $event->title)
@section('subheading', $event->campus?->name.' · '.$event->event_date->translatedFormat('l, d F Y'))

@section('actions')
    <a class="mod-btn ghost" href="{{ route('campus.events.index') }}">← Kembali</a>
    @if($canManage)
        <a class="mod-btn" href="{{ route('campus.events.edit', $event->id) }}">Edit Kegiatan</a>
    @endif
@endsection

@section('content')
    <div class="mod-panel">
        <dl class="mod-dl">
            <dt>Status</dt><dd><span class="mod-badge {{ $event->status }}">{{ $event->status_label }}</span> @if($event->visibility === 'public')<span class="mod-badge info">Publik</span>@endif</dd>
            <dt>Waktu</dt><dd>{{ substr($event->start_time, 0, 5) }}–{{ substr($event->end_time, 0, 5) }} WIB</dd>
            <dt>Lokasi</dt><dd>{{ $event->location }}</dd>
            <dt>Penyelenggara</dt><dd>{{ $event->organizer?->name ?? '—' }} ({{ $event->campus?->name }})</dd>
            <dt>Kuota</dt><dd>{{ $used }} terdaftar dari {{ $event->quota }} kursi (sisa {{ max(0, $event->quota - $used) }})</dd>
            <dt>Deskripsi</dt><dd>{!! nl2br(e($event->description)) !!}</dd>
        </dl>
    </div>

    @if($u->role === 'mahasiswa')
        <div class="mod-panel">
            <h3>Pendaftaran</h3>
            @if($isActiveReg)
                <p>Kamu <strong>sudah terdaftar</strong> pada kegiatan ini ({{ \App\Models\EventRegistration::STATUSES[$myRegistration->status] }}).</p>
                @if($myRegistration->status === 'registered' && ! $past)
                    <form method="POST" action="{{ route('campus.events.unregister', $event->id) }}" onsubmit="return confirm('Batalkan pendaftaran kegiatan ini?')">
                        @csrf @method('DELETE')
                        <button class="mod-btn danger" type="submit">Batalkan Pendaftaran</button>
                    </form>
                @endif
            @elseif($event->status !== 'published')
                <div class="mod-alert info">Pendaftaran untuk kegiatan ini tidak dibuka (status: {{ $event->status_label }}).</div>
            @elseif($past)
                <div class="mod-alert info">Kegiatan ini sudah berlalu.</div>
            @elseif($full)
                <div class="mod-alert error">Kuota peserta sudah penuh.</div>
            @else
                <form method="POST" action="{{ route('campus.events.register', $event->id) }}">
                    @csrf
                    <button class="mod-btn gold" type="submit">Daftar Kegiatan Ini</button>
                </form>
            @endif
        </div>
    @endif

    @if($canManage)
        <div class="mod-panel">
            <h3>Daftar Peserta ({{ $registrations->count() }})</h3>
            <div class="mod-table-wrap">
                <table class="mod-table">
                    <thead><tr><th>No.</th><th>Nama</th><th>Kampus Asal</th><th>Didaftarkan</th><th>Status</th><th>Ubah Status</th></tr></thead>
                    <tbody>
                        @forelse($registrations as $reg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reg->user?->name }}<br><small>{{ $reg->user?->email }}</small></td>
                                <td>{{ $reg->user?->campus?->name ?? '—' }}</td>
                                <td>{{ $reg->registered_at?->translatedFormat('d M Y H:i') }}</td>
                                <td><span class="mod-badge {{ $reg->status }}">{{ \App\Models\EventRegistration::STATUSES[$reg->status] ?? $reg->status }}</span></td>
                                <td>
                                    <form class="mod-inline-form" method="POST" action="{{ route('campus.registrations.update', $reg->id) }}">
                                        @csrf @method('PATCH')
                                        <select name="status">
                                            @foreach(\App\Models\EventRegistration::STATUSES as $k => $l)
                                                <option value="{{ $k }}" @selected($reg->status === $k)>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                        <button class="mod-btn ghost sm" type="submit">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="mod-empty"><strong>Belum ada peserta</strong>Belum ada mahasiswa yang mendaftar.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
