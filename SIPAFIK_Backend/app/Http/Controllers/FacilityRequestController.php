<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use App\Models\Laboratory;
use App\Models\LabSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FacilityRequestController extends Controller
{
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

        $reviewers = ['admin_kampus', 'petugas_fasilitas'];
        $requesters = ['mahasiswa', 'dosen', 'organisasi'];

        if ($review) {
            abort_unless(in_array($user->role, $reviewers, true), 403);
        } else {
            abort_unless(
                in_array($user->role, array_merge($reviewers, $requesters), true),
                403
            );
        }
    }

    private function campusRequests()
    {
        return FacilityRequest::where('campus_id', auth()->user()->campus_id);
    }

    public function index()
    {
        $this->checkAccess();

        $user = auth()->user();
        $query = $this->campusRequests()->with(['user', 'laboratory', 'approver']);

        if (!in_array($user->role, ['admin_kampus', 'petugas_fasilitas'], true)) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->latest()->paginate(12);

        return view('facility-requests.index', compact('requests'));
    }

    public function create()
    {
        $this->checkAccess();

        abort_unless(
            in_array(auth()->user()->role, ['mahasiswa', 'dosen', 'organisasi'], true),
            403
        );

        $laboratories = Laboratory::where('campus_id', auth()->user()->campus_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('facility-requests.form', compact('laboratories'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        abort_unless(
            in_array(auth()->user()->role, ['mahasiswa', 'dosen', 'organisasi'], true),
            403
        );

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
            'purpose' => ['required', 'string'],
            'request_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'participants_count' => ['required', 'integer', 'min:1'],
        ]);

        $validated['campus_id'] = $campusId;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['approved_by'] = null;
        $validated['review_notes'] = null;

        FacilityRequest::create($validated);

        return redirect()
            ->route('campus.facility-requests.index')
            ->with('success', 'Permohonan fasilitas berhasil dikirim.');
    }

    public function review(Request $request, int $id)
    {
        $this->checkAccess(true);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $facilityRequest = $this->campusRequests()->findOrFail($id);

        abort_unless($facilityRequest->status === 'pending', 403, 'Permohonan sudah diproses.');

        if ($validated['status'] === 'approved') {
            $this->ensureAvailable($facilityRequest);
        }

        $facilityRequest->update([
            'status' => $validated['status'],
            'approved_by' => auth()->id(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return redirect()
            ->route('campus.facility-requests.index')
            ->with('success', 'Permohonan berhasil diproses.');
    }

    private function ensureAvailable(FacilityRequest $request): void
    {
        $scheduleConflict = LabSchedule::where('campus_id', $request->campus_id)
            ->where('laboratory_id', $request->laboratory_id)
            ->whereDate('schedule_date', $request->request_date)
            ->where('status', 'scheduled')
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        $requestConflict = FacilityRequest::where('campus_id', $request->campus_id)
            ->where('laboratory_id', $request->laboratory_id)
            ->whereDate('request_date', $request->request_date)
            ->where('status', 'approved')
            ->where('id', '!=', $request->id)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($scheduleConflict || $requestConflict) {
            throw ValidationException::withMessages([
                'status' => 'Jadwal fasilitas bertabrakan dengan jadwal lain. Permohonan belum dapat disetujui.',
            ]);
        }
    }
}
