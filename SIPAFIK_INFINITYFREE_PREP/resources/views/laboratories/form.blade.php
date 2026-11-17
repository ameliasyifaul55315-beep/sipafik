@extends('layouts.dashboard')

@php $editing = isset($laboratory); @endphp

@section('title', $editing ? 'Edit Laboratorium' : 'Tambah Laboratorium')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', 'DATA FASILITAS KAMPUS')
@section('heading', $editing ? 'Edit Laboratorium' : 'Tambah Laboratorium')
@section('subheading', 'Lengkapi informasi laboratorium kampus dengan benar.')

@section('content')
    <div class="mod-panel" style="max-width:760px">
        <form class="mod-form" method="POST" action="{{ $editing ? route('campus.laboratories.update', $laboratory->id) : route('campus.laboratories.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif

            <div class="mod-grid-2">
                <div class="mod-field">
                    <label for="code">Kode Laboratorium</label>
                    <input id="code" name="code" value="{{ old('code', $laboratory->code ?? '') }}" required maxlength="30" placeholder="Contoh: LAB-001">
                    <div class="hint">Kode harus unik di dalam kampus Anda (maks. 30 karakter).</div>
                </div>
                <div class="mod-field">
                    <label for="capacity">Kapasitas (orang)</label>
                    <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $laboratory->capacity ?? 30) }}" required>
                </div>
            </div>

            <div class="mod-field">
                <label for="name">Nama Laboratorium</label>
                <input id="name" name="name" value="{{ old('name', $laboratory->name ?? '') }}" required placeholder="Contoh: Laboratorium Komputer">
            </div>
            <div class="mod-field">
                <label for="location">Lokasi</label>
                <input id="location" name="location" value="{{ old('location', $laboratory->location ?? '') }}" required placeholder="Contoh: Gedung Fakultas Teknik">
            </div>
            <div class="mod-field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" placeholder="Keterangan penggunaan laboratorium">{{ old('description', $laboratory->description ?? '') }}</textarea>
            </div>
            <div class="mod-field">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" name="facilities" placeholder="Pisahkan dengan koma, contoh: Komputer, Proyektor, AC, WiFi">{{ old('facilities', $editing ? implode(', ', $laboratory->facilities ?? []) : '') }}</textarea>
            </div>
            <div class="mod-field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active" @selected(old('status', $laboratory->status ?? 'active') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $laboratory->status ?? '') === 'inactive')>Nonaktif</option>
                </select>
                <div class="hint">Laboratorium nonaktif tidak dapat dipilih untuk permohonan baru.</div>
            </div>

            <div class="mod-form-actions">
                <button class="mod-btn" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Simpan Laboratorium' }}</button>
                <a class="mod-btn ghost" href="{{ route('campus.laboratories.index') }}">Kembali</a>
            </div>
        </form>
    </div>
@endsection
