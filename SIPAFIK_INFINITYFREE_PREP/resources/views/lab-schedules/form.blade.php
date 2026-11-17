@extends('layouts.dashboard')

@php $editing = isset($schedule); @endphp

@section('title', $editing ? 'Edit Jadwal Laboratorium' : 'Tambah Jadwal Laboratorium')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', 'PENGATURAN RUANG')
@section('heading', $editing ? 'Edit Jadwal Laboratorium' : 'Tambah Jadwal Laboratorium')
@section('subheading', 'Jadwal tidak boleh bertabrakan dengan jadwal atau permohonan yang sudah disetujui.')

@section('content')
    <div class="mod-panel" style="max-width:760px">
        @if($laboratories->isEmpty())
            <div class="mod-alert info">Belum ada laboratorium aktif. Tambahkan atau aktifkan laboratorium terlebih dahulu.</div>
        @elseif($lecturers->isEmpty())
            <div class="mod-alert info">Belum ada akun dosen aktif di kampus ini. Tambahkan akun dosen terlebih dahulu.</div>
        @else
            <form class="mod-form" method="POST" action="{{ $editing ? route('campus.lab-schedules.update', $schedule->id) : route('campus.lab-schedules.store') }}">
                @csrf
                @if($editing) @method('PUT') @endif

                <div class="mod-grid-2">
                    <div class="mod-field">
                        <label for="laboratory_id">Laboratorium</label>
                        <select name="laboratory_id" id="laboratory_id" required>
                            <option value="">Pilih laboratorium</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->id }}" @selected((string) old('laboratory_id', $schedule->laboratory_id ?? '') === (string) $lab->id)>{{ $lab->name }} — {{ $lab->location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mod-field">
                        <label for="lecturer_id">Dosen Penanggung Jawab</label>
                        <select name="lecturer_id" id="lecturer_id" required>
                            <option value="">Pilih dosen</option>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" @selected((string) old('lecturer_id', $schedule->lecturer_id ?? '') === (string) $lecturer->id)>{{ $lecturer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mod-grid-2">
                    <div class="mod-field">
                        <label for="course_name">Nama Mata Kuliah / Kegiatan</label>
                        <input name="course_name" id="course_name" value="{{ old('course_name', $schedule->course_name ?? '') }}" required>
                    </div>
                    <div class="mod-field">
                        <label for="class_name">Kelas</label>
                        <input name="class_name" id="class_name" value="{{ old('class_name', $schedule->class_name ?? '') }}" required>
                    </div>
                </div>

                <div class="mod-grid-3">
                    <div class="mod-field">
                        <label for="schedule_date">Tanggal</label>
                        <input type="date" name="schedule_date" id="schedule_date" @unless($editing) min="{{ now()->toDateString() }}" @endunless value="{{ old('schedule_date', $editing ? $schedule->schedule_date->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="mod-field">
                        <label for="start_time">Waktu Mulai</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $editing ? substr($schedule->start_time, 0, 5) : '') }}" required>
                    </div>
                    <div class="mod-field">
                        <label for="end_time">Waktu Selesai</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $editing ? substr($schedule->end_time, 0, 5) : '') }}" required>
                    </div>
                </div>

                <div class="mod-grid-2">
                    <div class="mod-field">
                        <label for="schedule_type">Jenis Jadwal</label>
                        <select name="schedule_type" id="schedule_type" required>
                            <option value="">Pilih jenis</option>
                            @foreach(['praktikum', 'perkuliahan', 'kegiatan', 'lainnya'] as $type)
                                <option value="{{ $type }}" @selected(old('schedule_type', $schedule->schedule_type ?? '') === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($editing)
                        <div class="mod-field">
                            <label for="status">Status</label>
                            <select name="status" id="status" required>
                                @foreach(['scheduled' => 'Terjadwal', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $schedule->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="mod-field">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes">{{ old('notes', $schedule->notes ?? '') }}</textarea>
                </div>

                <div class="mod-form-actions">
                    <button class="mod-btn" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Simpan Jadwal' }}</button>
                    <a class="mod-btn ghost" href="{{ route('campus.lab-schedules.index') }}">Kembali</a>
                </div>
            </form>
        @endif
    </div>
@endsection
