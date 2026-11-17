@extends('layouts.dashboard')

@section('title', 'Laboratorium')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', 'DATA FASILITAS KAMPUS')
@section('heading', 'Laboratorium')
@section('subheading', 'Kelola informasi ruang laboratorium dan fasilitas pendukung kampus.')

@php $canManage = auth()->user()->isFacilityManager(); @endphp

@section('actions')
    @if($canManage)
        <a class="mod-btn" href="{{ route('campus.laboratories.create') }}">+ Tambah Laboratorium</a>
    @endif
@endsection

@section('content')
    <div class="mod-panel">
        <div class="mod-table-wrap">
            <table class="mod-table">
                <thead>
                    <tr>
                        <th>Kode</th><th>Nama Laboratorium</th><th>Lokasi</th><th>Kapasitas</th><th>Status</th>
                        @if($canManage)<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($laboratories as $lab)
                        <tr>
                            <td>{{ $lab->code }}</td>
                            <td>
                                <strong>{{ $lab->name }}</strong>
                                <br><small>{{ implode(', ', $lab->facilities ?? []) }}</small>
                            </td>
                            <td>{{ $lab->location }}</td>
                            <td>{{ $lab->capacity }} orang</td>
                            <td><span class="mod-badge {{ $lab->status === 'active' ? 'active' : 'inactive' }}">{{ $lab->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td>
                            @if($canManage)
                                <td>
                                    <div class="mod-inline-form">
                                        <a class="mod-btn ghost sm" href="{{ route('campus.laboratories.edit', $lab->id) }}">Edit</a>
                                        <form method="POST" action="{{ route('campus.laboratories.toggle-status', $lab->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="mod-btn ghost sm" type="submit">{{ $lab->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $canManage ? 6 : 5 }}"><div class="mod-empty"><strong>Belum ada laboratorium</strong>Data laboratorium untuk kampus ini belum tersedia.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $laboratories->links() }}
    </div>
@endsection
