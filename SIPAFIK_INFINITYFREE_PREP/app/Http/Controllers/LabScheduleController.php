<?php

namespace App\Http\Controllers;

use App\Models\LabSchedule;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabScheduleController extends Controller
{
    private function checkAccess(bool $manage = false): void
    {
        $user = auth()->user();

        abort_unless($user->is_active, 403, 'Akun Anda tidak aktif.');

        abort_unless(
            in_array($user->role, [
                'admin_kampus',
                'petugas_fasilitas',
                'dosen',
            ], true),
            403
        );

        abort_unless($user->campus_id, 403);

        $user->loadMissing('campus');

        abort_unless(
            $user->campus && $user->campus->status === 'approved',
            403,
            'Kampus belum disetujui.'
        );

        if ($manage) {
            abort_unless(
                in_array($user->role, ['admin_kampus', 'petugas_fasilitas'], true),
                403
            );
        }
    }

    private function schedules()
    {
        return LabSchedule::where('campus_id', auth()->user()->campus_id);
    }

    private function formData(?LabSchedule $schedule = null): array
    {
        $campusId = auth()->user()->campus_id;

        return [
            'laboratories' => Laboratory::where('campus_id', $campusId)
                ->where(function ($q) use ($schedule) {
                    $q->where('status', 'active');
                    if ($schedule) {
                        $q->orWhere('id', $schedule->laboratory_id);
                    }
                })
                ->orderBy('name')
                ->get(),

            'lecturers' => User::where('campus_id', $campusId)
                ->where('role', 'dosen')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    public function index()
    {
        $this->checkAccess();

        $schedules = $this->schedules()
            ->with(['laboratory', 'lecturer'])
            ->orderByDesc('schedule_date')
            ->orderBy('start_time')
            ->paginate(12);

        return view('lab-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $this->checkAccess(true);

        return view('lab-schedules.form', $this->formData());
    }

    public function store(Request $request)
    {
        $this->checkAccess(true);

        $campusId = auth()->user()->campus_id;

        $validated = $request->validate([
            'laboratory_id' => [
                'required',
                Rule::exists('laboratories', 'id')->where(
                    fn ($query) => $query
                        ->where('campus_id', $campusId)
                        ->where('status', 'active')
                ),
            ],
            'lecturer_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query
                        ->where('campus_id', $campusId)
                        ->where('role', 'dosen')
                        ->where('is_active', true)
                ),
            ],
            'course_name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:100'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'schedule_type' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->ensureNoConflict($validated);

        $validated['campus_id'] = $campusId;
        $validated['status'] = 'scheduled';

        LabSchedule::create($validated);

        return redirect()
            ->route('campus.lab-schedules.index')
            ->with('success', 'Jadwal laboratorium berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $this->checkAccess(true);

        $schedule = $this->schedules()->findOrFail($id);

        return view(
            'lab-schedules.form',
            array_merge($this->formData($schedule), compact('schedule'))
        );
    }

    public function update(Request $request, int $id)
    {
        $this->checkAccess(true);

        $schedule = $this->schedules()->findOrFail($id);
        $campusId = auth()->user()->campus_id;

        $currentLabId = $schedule->laboratory_id;

        $validated = $request->validate([
            'laboratory_id' => [
                'required',
                // Boleh tetap memakai laboratorium yang sama walau sudah dinonaktifkan; pindah lab hanya ke lab aktif.
                Rule::exists('laboratories', 'id')->where(
                    fn ($query) => $query
                        ->where('campus_id', $campusId)
                        ->where(fn ($q) => $q->where('status', 'active')->orWhere('id', $currentLabId))
                ),
            ],
            'lecturer_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query
                        ->where('campus_id', $campusId)
                        ->where('role', 'dosen')
                        ->where('is_active', true)
                ),
            ],
            'course_name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:100'],
            'schedule_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'schedule_type' => ['required', 'string', 'max:30'],
            'status' => [
                'required',
                Rule::in(['scheduled', 'completed', 'cancelled']),
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $this->ensureNoConflict($validated, $schedule->id);

        $schedule->update($validated);

        return redirect()
            ->route('campus.lab-schedules.index')
            ->with('success', 'Jadwal laboratorium berhasil diperbarui.');
    }

    private function ensureNoConflict(array $data, ?int $exceptId = null): void
    {
        $conflict = $this->schedules()
            ->where('laboratory_id', $data['laboratory_id'])
            ->whereDate('schedule_date', $data['schedule_date'])
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();

        // Permohonan fasilitas yang sudah DISETUJUI juga memakai laboratorium pada waktu tersebut.
        $requestConflict = \App\Models\FacilityRequest::where('campus_id', auth()->user()->campus_id)
            ->where('laboratory_id', $data['laboratory_id'])
            ->whereDate('request_date', $data['schedule_date'])
            ->where('status', 'approved')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        // Jadwal yang dibatalkan/selesai tidak dihitung bentrok bila statusnya cancelled; edit ke cancelled selalu boleh.
        if (($data['status'] ?? 'scheduled') === 'cancelled') {
            return;
        }

        if ($conflict || $requestConflict) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_time' => $requestConflict && ! $conflict
                    ? 'Waktu tersebut sudah dipakai oleh permohonan fasilitas yang disetujui.'
                    : 'Jadwal bertabrakan dengan penggunaan laboratorium yang sudah tercatat.',
            ]);
        }
    }
}
