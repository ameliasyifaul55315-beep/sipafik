@extends('layouts.dashboard')

@php $editing = isset($event); @endphp

@section('title', $editing ? 'Edit Kegiatan' : 'Buat Kegiatan')
@section('eyebrow', 'KEGIATAN KAMPUS')
@section('kicker', 'PENYELENGGARAAN KEGIATAN')
@section('heading', $editing ? 'Edit Kegiatan' : 'Buat Kegiatan Baru')
@section('subheading', 'Kegiatan berstatus Dibuka akan tampil untuk mahasiswa. Pilih visibilitas Publik agar dapat diikuti mahasiswa kampus lain.')

@section('content')
    <div class="mod-panel" style="max-width:820px">
        <form class="mod-form" method="POST" action="{{ $editing ? route('campus.events.update', $event->id) : route('campus.events.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif

            <div class="mod-field">
                <label for="title">Judul Kegiatan</label>
                <input id="title" name="title" value="{{ old('title', $event->title ?? '') }}" maxlength="255" required>
            </div>
            <div class="mod-field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" required>{{ old('description', $event->description ?? '') }}</textarea>
            </div>

            <div class="mod-grid-2">
                <div class="mod-field">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('category', $event->category ?? 'seminar') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mod-field">
                    <label for="location">Lokasi</label>
                    <input id="location" name="location" value="{{ old('location', $event->location ?? '') }}" maxlength="255" required>
                </div>
            </div>

            <div class="mod-grid-3">
                <div class="mod-field">
                    <label for="event_date">Tanggal</label>
                    <input id="event_date" type="date" name="event_date" @unless($editing) min="{{ now()->toDateString() }}" @endunless value="{{ old('event_date', $editing ? $event->event_date->format('Y-m-d') : '') }}" required>
                </div>
                <div class="mod-field">
                    <label for="start_time">Waktu Mulai</label>
                    <input id="start_time" type="time" name="start_time" value="{{ old('start_time', $editing ? substr($event->start_time, 0, 5) : '') }}" required>
                </div>
                <div class="mod-field">
                    <label for="end_time">Waktu Selesai</label>
                    <input id="end_time" type="time" name="end_time" value="{{ old('end_time', $editing ? substr($event->end_time, 0, 5) : '') }}" required>
                </div>
            </div>

            <div class="mod-grid-3">
                <div class="mod-field">
                    <label for="quota">Kuota Peserta</label>
                    <input id="quota" type="number" min="1" name="quota" value="{{ old('quota', $event->quota ?? 50) }}" required>
                </div>
                <div class="mod-field">
                    <label for="visibility">Visibilitas</label>
                    <select id="visibility" name="visibility" required>
                        <option value="campus" @selected(old('visibility', $event->visibility ?? 'campus') === 'campus')>Kampus sendiri</option>
                        <option value="public" @selected(old('visibility', $event->visibility ?? '') === 'public')>Publik (kampus lain dapat mendaftar)</option>
                    </select>
                </div>
                <div class="mod-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @php $allowed = $editing ? \App\Models\Event::STATUSES : ['draft' => 'Draf (belum tampil)', 'published' => 'Dibuka']; @endphp
                        @foreach($allowed as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $event->status ?? 'published') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mod-form-actions">
                <button class="mod-btn" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}</button>
                <a class="mod-btn ghost" href="{{ $editing ? route('campus.events.show', $event->id) : route('campus.events.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
