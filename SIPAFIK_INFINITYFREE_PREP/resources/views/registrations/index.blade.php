@extends('layouts.dashboard')

@php
    $u = auth()->user();
    $isStudent = $u->role === 'mahasiswa';
@endphp

@section('title', $isStudent ? 'Pendaftaran Saya' : 'Pendaftar Kegiatan')
@section('eyebrow', 'KEGIATAN KAMPUS')
@section('kicker', $isStudent ? 'RIWAYAT PENDAFTARAN' : 'PEMANTAUAN PESERTA')
@section('heading', $isStudent ? 'Pendaftaran Saya' : 'Pendaftar Kegiatan')
@section('subheading', $isStudent
    ? 'Daftar kegiatan yang kamu ikuti beserta statusnya.'
    : 'Pantau mahasiswa yang mendaftar pada kegiatan yang Anda kelola dan tandai kehadirannya.')

@section('content')
    <form class="mod-filter" method="GET" action="{{ route('campus.registrations.index') }}">
        @unless($isStudent)
            <select name="event_id">
                <option value="">Semua kegiatan</option>
                @foreach($events as $e)
                    <option value="{{ $e->id }}" @selected((string) request('event_id') === (string) $e->id)>{{ $e->title }} ({{ $e->event_date->format('d/m/Y') }})</option>
                @endforeach
            </select>
        @endunless
        <select name="status">
            <option value="">Semua status</option>
            @foreach(\App\Models\EventRegistration::STATUSES as $k => $l)
                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
            @endforeach
        </select>
        <button class="mod-btn ghost sm" type="submit">Terapkan</button>
    </form>

    <div class="mod-panel">
        <div class="mod-table-wrap">
            <table class="mod-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        @unless($isStudent)<th>Peserta</th>@endunless
                        <th>Kegiatan</th><th>Tanggal</th><th>Didaftarkan</th><th>Status</th>
                        @unless($isStudent)<th>Ubah Status</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td>{{ $loop->iteration + ($registrations->currentPage() - 1) * $registrations->perPage() }}</td>
                            @unless($isStudent)
                                <td>{{ $reg->user?->name }}<br><small>{{ $reg->user?->campus?->name }}</small></td>
                            @endunless
                            <td>
                                <a href="{{ route('campus.events.show', $reg->event_id) }}"><strong>{{ $reg->event?->title ?? 'Kegiatan dihapus' }}</strong></a>
                                <br><small>{{ $reg->event?->campus?->name }}</small>
                            </td>
                            <td>{{ $reg->event?->event_date?->translatedFormat('d M Y') }}<br><small>{{ $reg->event ? substr($reg->event->start_time, 0, 5) : '' }}</small></td>
                            <td>{{ $reg->registered_at?->translatedFormat('d M Y H:i') }}</td>
                            <td><span class="mod-badge {{ $reg->status }}">{{ \App\Models\EventRegistration::STATUSES[$reg->status] ?? $reg->status }}</span></td>
                            @unless($isStudent)
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
                            @endunless
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isStudent ? 5 : 7 }}"><div class="mod-empty"><strong>Belum ada data</strong>{{ $isStudent ? 'Kamu belum mendaftar kegiatan apa pun.' : 'Belum ada pendaftar.' }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $registrations->links() }}
    </div>
@endsection
