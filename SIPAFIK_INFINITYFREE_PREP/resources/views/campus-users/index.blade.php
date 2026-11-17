@extends('layouts.dashboard')

@section('title', 'Pengguna Kampus')
@section('eyebrow', 'ADMINISTRASI KAMPUS')
@section('kicker', 'DATA PENGGUNA')
@section('heading', 'Pengguna Kampus')
@section('subheading', 'Kelola akun dosen, organisasi, petugas fasilitas, dan mahasiswa di kampus Anda.')

@section('actions')
    <a class="mod-btn" href="{{ route('campus.users.create') }}">+ Tambah Pengguna</a>
@endsection

@section('content')
    <div class="mod-stats">
        @foreach($roles as $key => $label)
            <div class="mod-stat"><span>{{ $label }}</span><strong>{{ $counts[$key] ?? 0 }}</strong><small>akun</small></div>
        @endforeach
    </div>

    <form class="mod-filter" method="GET" action="{{ route('campus.users.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau username…">
        <select name="role">
            <option value="">Semua role</option>
            @foreach($roles as $key => $label)
                <option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>
            @endforeach
            <option value="admin_kampus" @selected(request('role') === 'admin_kampus')>Admin Kampus</option>
        </select>
        <select name="status">
            <option value="">Semua status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
        </select>
        <button class="mod-btn ghost sm" type="submit">Terapkan</button>
    </form>

    <div class="mod-panel">
        <div class="mod-table-wrap">
            <table class="mod-table">
                <thead><tr><th>Nama</th><th>Email</th><th>Username</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($users as $row)
                        <tr>
                            <td><strong>{{ $row->name }}</strong></td>
                            <td>{{ $row->email }}</td>
                            <td>{{ $row->username }}</td>
                            <td>{{ $row->role_label }}</td>
                            <td><span class="mod-badge {{ $row->is_active ? 'active' : 'inactive' }}">{{ $row->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                @if(array_key_exists($row->role, $roles))
                                    <form method="POST" action="{{ route('campus.users.toggle-status', $row->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="mod-btn ghost sm" type="submit">{{ $row->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                @else
                                    <small>—</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="mod-empty"><strong>Tidak ada pengguna</strong>Tidak ada akun yang cocok dengan filter.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
@endsection
