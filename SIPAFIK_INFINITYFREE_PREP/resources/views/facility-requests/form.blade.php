@extends('layouts.dashboard')

@section('title', 'Ajukan Fasilitas')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', 'PERMOHONAN BARU')
@section('heading', 'Ajukan Penggunaan Fasilitas')
@section('subheading', 'Lengkapi informasi berikut. Jumlah peserta tidak boleh melebihi kapasitas ruang.')

@section('content')
    <div class="mod-panel" style="max-width:760px">
        @if($laboratories->isEmpty())
            <div class="mod-alert info">Belum tersedia laboratorium aktif di kampus Anda. Silakan hubungi Admin Kampus atau Petugas Fasilitas.</div>
            <a href="{{ route('campus.facility-requests.index') }}" class="mod-btn ghost">Kembali</a>
        @else
            <form class="mod-form" method="POST" action="{{ route('campus.facility-requests.store') }}">
                @csrf
                <div class="mod-field">
                    <label for="laboratory_id">Fasilitas / Laboratorium</label>
                    <select id="laboratory_id" name="laboratory_id" required>
                        <option value="">-- Pilih fasilitas --</option>
                        @foreach($laboratories as $laboratory)
                            <option value="{{ $laboratory->id }}" @selected(old('laboratory_id') == $laboratory->id)>{{ $laboratory->name }} — kapasitas {{ $laboratory->capacity }} orang</option>
                        @endforeach
                    </select>
                </div>
                <div class="mod-field">
                    <label for="title">Judul Kegiatan</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" maxlength="255" required>
                </div>
                <div class="mod-field">
                    <label for="purpose">Tujuan / Keperluan</label>
                    <textarea id="purpose" name="purpose" required>{{ old('purpose') }}</textarea>
                </div>
                <div class="mod-grid-3">
                    <div class="mod-field">
                        <label for="request_date">Tanggal Penggunaan</label>
                        <input id="request_date" type="date" name="request_date" value="{{ old('request_date') }}" min="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mod-field">
                        <label for="start_time">Waktu Mulai</label>
                        <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required>
                    </div>
                    <div class="mod-field">
                        <label for="end_time">Waktu Selesai</label>
                        <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required>
                    </div>
                </div>
                <div class="mod-field">
                    <label for="participants_count">Jumlah Peserta</label>
                    <input id="participants_count" type="number" name="participants_count" min="1" value="{{ old('participants_count', 1) }}" required>
                </div>

                <div class="mod-alert info">Permohonan akan berstatus <strong>Menunggu</strong> sampai diperiksa oleh Admin Kampus atau Petugas Fasilitas.</div>

                <div class="mod-form-actions">
                    <button type="submit" class="mod-btn">Kirim Permohonan</button>
                    <a href="{{ route('campus.facility-requests.index') }}" class="mod-btn ghost">Kembali</a>
                </div>
            </form>
        @endif
    </div>
@endsection
