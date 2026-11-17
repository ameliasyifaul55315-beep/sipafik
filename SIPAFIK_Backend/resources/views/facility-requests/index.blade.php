<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Fasilitas | SIPAFIK</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        body { background:#f5f3eb; color:#26352d; }
        .page { max-width:1200px; margin:35px auto; padding:24px; }
        .panel { background:#fff; padding:25px; border-radius:16px; box-shadow:0 5px 24px #26352d12; margin-top:20px; }
        h1 { color:#244b38; }
        .top { display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap; }
        .btn { display:inline-block; padding:10px 16px; border-radius:8px; background:#315b43; color:white; text-decoration:none; border:0; cursor:pointer; }
        .btn-secondary { background:#777; }
        .btn-danger { background:#9b4545; }
        .success { padding:12px; background:#e5f3e8; color:#245a35; border-radius:8px; margin:15px 0; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; min-width:850px; }
        th,td { padding:13px; border-bottom:1px solid #e8e8e1; text-align:left; vertical-align:top; }
        th { background:#f0f1e9; color:#315b43; }
        .status { display:inline-block; padding:5px 10px; border-radius:20px; font-size:12px; background:#eee; }
        .pending { background:#fff0ce; color:#805800; }
        .approved { background:#dff1e3; color:#24643a; }
        .rejected { background:#f8e1e1; color:#963c3c; }
        textarea,select { width:100%; padding:8px; border:1px solid #d5d8cf; border-radius:7px; margin:5px 0 8px; }
        .review-form { min-width:180px; }
        .muted { color:#777; }
        .errors { color:#a32d2d; }
    </style>
</head>
<body>
<div class="page">
    <div class="top">
        <div>
            <h1>Permohonan Fasilitas</h1>
            <p class="muted">Kelola dan pantau permohonan penggunaan fasilitas kampus.</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
            @if(in_array(auth()->user()->role, ['mahasiswa', 'dosen', 'organisasi'], true))
                <a href="{{ route('campus.facility-requests.create') }}" class="btn">+ Ajukan Fasilitas</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="panel errors">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pemohon</th>
                        <th>Fasilitas</th>
                        <th>Keperluan</th>
                        <th>Tanggal & Waktu</th>
                        <th>Peserta</th>
                        <th>Status</th>
                        <th>Catatan / Proses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}</td>
                            <td>{{ $request->user?->name ?? 'Pengguna tidak ditemukan' }}</td>
                            <td>{{ $request->laboratory?->name ?? 'Fasilitas tidak ditemukan' }}</td>
                            <td>
                                <strong>{{ $request->title }}</strong>
                                <p>{{ $request->purpose }}</p>
                            </td>
                            <td>
                                {{ $request->request_date?->format('d/m/Y') }}
                                <br>
                                {{ substr($request->start_time, 0, 5) }}–{{ substr($request->end_time, 0, 5) }}
                            </td>
                            <td>{{ $request->participants_count }}</td>
                            <td>
                                <span class="status {{ $request->status }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>
                                @if(in_array(auth()->user()->role, ['admin_kampus', 'petugas_fasilitas'], true) && $request->status === 'pending')
                                    <form class="review-form" method="POST" action="{{ route('campus.facility-requests.review', $request->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" required>
                                            <option value="approved">Setujui</option>
                                            <option value="rejected">Tolak</option>
                                        </select>
                                        <textarea name="review_notes" rows="2" placeholder="Catatan untuk pemohon (opsional)"></textarea>
                                        <button class="btn" type="submit">Proses</button>
                                    </form>
                                @else
                                    <span>{{ $request->review_notes ?: '-' }}</span>
                                    @if($request->approver)
                                        <p class="muted">Diproses oleh: {{ $request->approver->name }}</p>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:30px;">
                                Belum ada permohonan fasilitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:20px;">
            {{ $requests->links() }}
        </div>
    </div>
</div>
</body>
</html>