@extends('layouts.dashboard')

@section('title', 'Jadwal Laboratorium')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', 'PENGATURAN RUANG')
@section('heading', 'Jadwal Laboratorium')
@section('subheading', 'Pantau jadwal penggunaan laboratorium kampus. Sistem menolak jadwal yang bentrok.')

@php
    $canManage = auth()->user()->isFacilityManager();
    $statusLabels = ['scheduled' => 'Terjadwal', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
@endphp

@section('actions')
    @if($canManage)
        <a class="mod-btn" href="{{ route('campus.lab-schedules.create') }}">+ Tambah Jadwal</a>
    @endif
@endsection

@section('content')
    <div class="mod-panel">
        <div class="mod-table-wrap">
            <table class="mod-table">
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Laboratorium</th><th>Mata Kuliah</th><th>Kelas</th><th>Dosen</th><th>Waktu</th><th>Status</th>
                        @if($canManage)<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->schedule_date?->translatedFormat('d M Y') }}</td>
                            <td>{{ $schedule->laboratory?->name ?? 'Laboratorium tidak tersedia' }}</td>
                            <td><strong>{{ $schedule->course_name }}</strong><br><small>{{ ucfirst($schedule->schedule_type) }}</small></td>
                            <td>{{ $schedule->class_name }}</td>
                            <td>{{ $schedule->lecturer?->name ?? '—' }}</td>
                            <td>{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</td>
                            <td><span class="mod-badge {{ $schedule->status }}">{{ $statusLabels[$schedule->status] ?? ucfirst($schedule->status) }}</span></td>
                            @if($canManage)
                                <td><a class="mod-btn ghost sm" href="{{ route('campus.lab-schedules.edit', $schedule->id) }}">Edit</a></td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $canManage ? 8 : 7 }}"><div class="mod-empty"><strong>Belum ada jadwal</strong>Belum ada jadwal laboratorium untuk kampus ini.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $schedules->links() }}
    </div>
@endsection
