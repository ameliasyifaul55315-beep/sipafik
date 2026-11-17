@extends('layouts.dashboard')

@section('title', 'Permohonan Fasilitas')
@section('eyebrow', 'MANAJEMEN FASILITAS')
@section('kicker', $isReviewer ? 'PERMOHONAN MASUK' : 'PERMOHONAN SAYA')
@section('heading', 'Permohonan Fasilitas')
@section('subheading', $isReviewer
    ? 'Tinjau, setujui, atau tolak permohonan penggunaan fasilitas kampus. Penolakan wajib disertai alasan.'
    : 'Pantau status permohonan fasilitas milik Anda beserta catatan dari petugas.')

@php
    $statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
    $canRequest = in_array(auth()->user()->role, ['mahasiswa', 'dosen', 'organisasi'], true);
    $tabs = [
        'all' => 'Semua ('.$counts['all'].')',
        'pending' => 'Menunggu ('.$counts['pending'].')',
        'approved' => 'Disetujui ('.$counts['approved'].')',
        'rejected' => 'Ditolak ('.$counts['rejected'].')',
    ];
    if ($isReviewer) { $tabs['processed'] = 'Riwayat diproses'; }
@endphp

@section('actions')
    @if($canRequest)
        <a href="{{ route('campus.facility-requests.create') }}" class="mod-btn">+ Ajukan Fasilitas</a>
    @endif
@endsection

@section('content')
    <div class="mod-tabs">
        @foreach($tabs as $key => $label)
            <a class="{{ $status === $key ? 'active' : '' }}" href="{{ route('campus.facility-requests.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mod-panel">
        <div class="mod-table-wrap">
            <table class="mod-table" style="min-width:900px">
                <thead>
                    <tr>
                        <th>No.</th><th>Pemohon</th><th>Fasilitas</th><th>Keperluan</th><th>Tanggal &amp; Waktu</th><th>Peserta</th><th>Status</th><th>Catatan / Proses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}</td>
                            <td>{{ $request->user?->name ?? 'Pengguna tidak ditemukan' }}<br><small>{{ $request->user?->role_label }}</small></td>
                            <td>{{ $request->laboratory?->name ?? 'Fasilitas tidak ditemukan' }}</td>
                            <td><strong>{{ $request->title }}</strong><br><small>{{ \Illuminate\Support\Str::limit($request->purpose, 110) }}</small></td>
                            <td>{{ $request->request_date?->translatedFormat('d M Y') }}<br><small>{{ substr($request->start_time, 0, 5) }}–{{ substr($request->end_time, 0, 5) }}</small></td>
                            <td>{{ $request->participants_count }}</td>
                            <td><span class="mod-badge {{ $request->status }}">{{ $statusLabels[$request->status] ?? ucfirst($request->status) }}</span></td>
                            <td style="min-width:230px">
                                @if($isReviewer && $request->status === 'pending')
                                    <form class="mod-inline-form" method="POST" action="{{ route('campus.facility-requests.review', $request->id) }}" style="display:grid;gap:8px">
                                        @csrf @method('PATCH')
                                        <select name="status" required>
                                            <option value="approved">Setujui</option>
                                            <option value="rejected">Tolak</option>
                                        </select>
                                        <textarea name="review_notes" rows="2" placeholder="Catatan untuk pemohon (wajib jika menolak)"></textarea>
                                        <button class="mod-btn sm" type="submit">Proses</button>
                                    </form>
                                @else
                                    @if($request->review_notes)
                                        <span>{{ $request->review_notes }}</span>
                                    @elseif($request->status === 'pending')
                                        <small>Menunggu keputusan petugas.</small>
                                    @else
                                        <small>Tanpa catatan.</small>
                                    @endif
                                    @if($request->approver)
                                        <br><small>Diproses oleh {{ $request->approver->name }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="mod-empty"><strong>Belum ada permohonan</strong>Tidak ada permohonan pada filter ini.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    </div>
@endsection
