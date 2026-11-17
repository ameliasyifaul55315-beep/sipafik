<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use App\Models\Laboratory;
use App\Models\LabSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FacilityRequestController extends Controller
{
    private const REVIEWERS = ['admin_kampus', 'petugas_fasilitas'];
    private const REQUESTERS = ['mahasiswa', 'dosen', 'organisasi'];

    private function checkAccess(bool $review = false): void
    {
        $user = auth()->user();

        abort_unless($user && $user->is_active, 403, 'Akun tidak aktif.');
        abort_unless($user->campus_id, 403, 'Akun belum terhubung ke kampus.');

        $user->loadMissing('campus');

        abort_unless(
            $user->campus && $user->campus->status === 'approved',
            403,
            'Kampus belum disetujui.'
        );

        if ($review) {
            abort_unless(in_array($user->role, self::REVIEWERS, true), 403);
        } else {
            abort_unless(
                in_array($user->role, array_merge(self::REVIEWERS, self::REQUESTERS), true),
                403
            );
        }
    }

    private function campusRequests()
    {
        return FacilityRequest::where('campus_id', auth()->user()->campus_id);
    }

    public function index(Request $request)
    {
        $this->checkAccess();

        $user = auth()->user();
        $isReviewer = in_array($user->role, self::REVIEWERS, true);

        $base = $this->campusRequests();
        if (! $isReviewer) {
            $base->where('user_id', $user->id);
        }

        $counts = [
            'all' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];

        $query = (clone $base)->with(['user', 'laboratory', 'approver']);

        $status = $request->query('status', 'all');
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        } elseif ($status === 'processed') {
            // "Riwayat": permohonan yang sudah diputuskan.
            $query->whereIn('status', ['approved', 'rejected']);
        } else {
            $status = 'all';
        }

        $requests = $query->latest()->paginate(12)->withQueryString();

        return view('facility-requests.index', compact('requests', 'counts', 'status', 'isReviewer'));
    }

    public function create()
    {
        $this->checkAccess();

        abort_unless(in_array(auth()->user()->role, self::REQUESTERS, true), 403);

        $laboratories = Laboratory::where('campus_id', auth()->user()->campus_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('facility-requests.form', compact('laboratories'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        abort_unless(in_array(auth()->user()->role, self::REQUESTERS, true), 403);

        $campusId = auth()->user()->campus_id;

        $validated = $request->validate([
            'laboratory_id' => [
                'required',
                Rule::exists('laboratories', 'id')
                    ->where(fn ($query) => $query
                        ->where('campus_id', $campusId)
                        ->where('status', 'active')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:3000'],
            'request_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'participants_count' => ['required', 'integer', 'min:1'],
        ], [
            'laboratory_id.exists' => 'Fasilitas yang dipilih tidak tersedia atau sedang nonaktif.',
        ]);

        $laboratory = Laboratory::where('campus_id', $campusId)->findOrFail($validated['laboratory_id']);

        // Validasi kapasitas.
        if ($validated['participants_count'] > $laboratory->capacity) {
            throw ValidationException::withMessages([
                'participants_count' => "Jumlah peserta melebihi kapasitas {$laboratory->name} ({$laboratory->capacity} orang).",
            ]);
        }

        // Jika hari ini, waktu mulai tidak boleh sudah lewat.
        if ($validated['request_date'] === today()->toDateString()
            && $validated['start_time'] <= now()->format('H:i')) {
            throw ValidationException::withMessages([
                'start_time' => 'Waktu mulai sudah lewat. Pilih waktu yang akan datang.',
            ]);
        }

        // Tolak lebih awal bila waktu sudah terpakai (jadwal / permohonan yang telah disetujui).
        $this->ensureAvailable(new FacilityRequest([
            'campus_id' => $campusId,
            'laboratory_id' => $laboratory->id,
            'request_date' => $validated['request_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]), 'start_time');

        $validated['campus_id'] = $campusId;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['approved_by'] = null;
        $validated['review_notes'] = null;

        FacilityRequest::create($validated);

        return redirect()
            ->route('campus.facility-requests.index')
            ->with('success', 'Permohonan fasilitas berhasil dikirim dan menunggu keputusan petugas.');
    }

    public function review(Request $request, int $id)
    {
        $this->checkAccess(true);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_notes' => [
                Rule::requiredIf(fn () => $request->input('status') === 'rejected'),
                'nullable', 'string', 'max:2000',
            ],
        ], [
            'review_notes.required' => 'Alasan penolakan wajib diisi agar pemohon mengetahui sebabnya.',
        ]);

        DB::transaction(function () use ($id, $validated) {
            $facilityRequest = $this->campusRequests()->lockForUpdate()->findOrFail($id);

            abort_unless($facilityRequest->status === 'pending', 403, 'Permohonan sudah diproses.');

            if ($validated['status'] === 'approved') {
                $laboratory = Laboratory::where('campus_id', $facilityRequest->campus_id)
                    ->lockForUpdate()
                    ->find($facilityRequest->laboratory_id);

                if (! $laboratory || $laboratory->status !== 'active') {
                    throw ValidationException::withMessages([
                        'status' => 'Laboratorium sedang nonaktif atau tidak ditemukan. Permohonan tidak dapat disetujui.',
                    ]);
                }

                if ($facilityRequest->participants_count > $laboratory->capacity) {
                    throw ValidationException::withMessages([
                        'status' => 'Jumlah peserta melebihi kapasitas laboratorium saat ini.',
                    ]);
                }

                $this->ensureAvailable($facilityRequest, 'status');
            }

            $facilityRequest->update([
                'status' => $validated['status'],
                'approved_by' => auth()->id(),
                'review_notes' => $validated['review_notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('campus.facility-requests.index')
            ->with('success', $validated['status'] === 'approved'
                ? 'Permohonan disetujui.'
                : 'Permohonan ditolak. Catatan dapat dilihat oleh pemohon.');
    }

    private function ensureAvailable(FacilityRequest $request, string $errorKey): void
    {
        $date = $request->request_date instanceof \DateTimeInterface
            ? $request->request_date->format('Y-m-d')
            : $request->request_date;

        $scheduleConflict = LabSchedule::where('campus_id', $request->campus_id)
            ->where('laboratory_id', $request->laboratory_id)
            ->whereDate('schedule_date', $date)
            ->where('status', 'scheduled')
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        $requestConflict = FacilityRequest::where('campus_id', $request->campus_id)
            ->where('laboratory_id', $request->laboratory_id)
            ->whereDate('request_date', $date)
            ->where('status', 'approved')
            ->when($request->exists, fn ($q) => $q->where('id', '!=', $request->id))
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($scheduleConflict || $requestConflict) {
            throw ValidationException::withMessages([
                $errorKey => 'Jadwal fasilitas bertabrakan dengan jadwal lain pada waktu tersebut.',
            ]);
        }
    }
}
