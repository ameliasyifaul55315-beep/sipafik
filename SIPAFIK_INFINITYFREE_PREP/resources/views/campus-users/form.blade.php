@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna')
@section('eyebrow', 'ADMINISTRASI KAMPUS')
@section('kicker', 'DATA PENGGUNA')
@section('heading', 'Tambah Pengguna Kampus')
@section('subheading', 'Akun baru otomatis terhubung ke kampus Anda dan langsung aktif.')

@section('content')
    <div class="mod-panel" style="max-width:720px">
        <form class="mod-form" method="POST" action="{{ route('campus.users.store') }}" autocomplete="off">
            @csrf
            <div class="mod-field">
                <label for="name">Nama Lengkap</label>
                <input id="name" name="name" value="{{ old('name') }}" required maxlength="255">
            </div>
            <div class="mod-grid-2">
                <div class="mod-field">
                    <label for="email">Email (untuk login)</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="mod-field">
                    <label for="username">Username</label>
                    <input id="username" name="username" value="{{ old('username') }}" required minlength="3" maxlength="30">
                    <div class="hint">Huruf, angka, strip, dan garis bawah.</div>
                </div>
            </div>
            <div class="mod-field">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Pilih role</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" @selected(old('role') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mod-grid-2">
                <div class="mod-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
                </div>
                <div class="mod-field">
                    <label for="password_confirmation">Ulangi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>
            </div>
            <div class="mod-form-actions">
                <button class="mod-btn" type="submit">Simpan Pengguna</button>
                <a class="mod-btn ghost" href="{{ route('campus.users.index') }}">Kembali</a>
            </div>
        </form>
    </div>
@endsection
