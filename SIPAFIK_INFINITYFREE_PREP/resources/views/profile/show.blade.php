@extends('layouts.dashboard')

@section('title', 'Profil & Kampus')
@section('eyebrow', 'AKUN SAYA')
@section('kicker', 'PROFIL PENGGUNA')
@section('heading', $user->name)
@section('subheading', $user->role_label.' · '.($campus?->name ?? 'Belum terhubung ke kampus'))

@section('content')
    <div class="mod-panel">
        <h3>Informasi Akun</h3>
        <dl class="mod-dl">
            <dt>Nama</dt><dd>{{ $user->name }}</dd>
            <dt>Email (login)</dt><dd>{{ $user->email }}</dd>
            <dt>Username</dt><dd>{{ $user->username }}</dd>
            <dt>Role</dt><dd>{{ $user->role_label }}</dd>
            <dt>Status Akun</dt><dd><span class="mod-badge {{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></dd>
            <dt>Bergabung</dt><dd>{{ $user->created_at?->translatedFormat('d F Y') ?? '—' }}</dd>
        </dl>
    </div>

    <div class="mod-panel">
        <h3>Informasi Kampus</h3>
        @if($campus)
            <dl class="mod-dl">
                <dt>Nama Kampus</dt><dd>{{ $campus->name }}</dd>
                <dt>Status Kampus</dt><dd><span class="mod-badge {{ $campus->status }}">{{ ['pending' => 'Menunggu verifikasi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$campus->status] ?? $campus->status }}</span></dd>
                <dt>Email Kampus</dt><dd>{{ $campus->email }}</dd>
                <dt>Telepon</dt><dd>{{ $campus->phone ?: '—' }}</dd>
                <dt>Alamat</dt><dd>{{ $campus->address }}</dd>
                <dt>Penanggung Jawab</dt><dd>{{ $campus->responsible_person }}</dd>
            </dl>
        @else
            <div class="mod-alert info">Akun Anda belum terhubung ke kampus mana pun.</div>
        @endif
    </div>
@endsection
