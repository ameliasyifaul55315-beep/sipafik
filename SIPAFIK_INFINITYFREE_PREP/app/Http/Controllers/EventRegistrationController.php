<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventRegistrationController extends Controller
{
    /**
     * Mahasiswa: pendaftaran milik sendiri.
     * Admin kampus: seluruh pendaftar kegiatan di kampusnya.
     * Organisasi & dosen: pendaftar pada kegiatan yang mereka selenggarakan.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, ['mahasiswa', 'admin_kampus', 'organisasi', 'dosen'], true),
            403
        );

        $query = EventRegistration::with(['event.campus', 'user.campus'])->latest('registered_at');

        if ($user->role === 'mahasiswa') {
            $query->where('user_id', $user->id);
        } else {
            $query->where('campus_id', $user->campus_id);

            if ($user->role !== 'admin_kampus') {
                $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->id));
            }
        }

        if ($request->filled('status') && array_key_exists($request->query('status'), EventRegistration::STATUSES)) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('event_id') && $user->role !== 'mahasiswa') {
            $query->where('event_id', (int) $request->query('event_id'));
        }

        $registrations = $query->paginate(12)->withQueryString();

        $events = $user->role === 'mahasiswa'
            ? collect()
            : Event::where('campus_id', $user->campus_id)
                ->when($user->role !== 'admin_kampus', fn ($q) => $q->where('organizer_id', $user->id))
                ->orderByDesc('event_date')
                ->get(['id', 'title', 'event_date']);

        return view('registrations.index', compact('registrations', 'events'));
    }

    /** Pengelola menandai peserta: hadir / batal / terdaftar. */
    public function update(Request $request, int $id)
    {
        $user = auth()->user();
        abort_unless(in_array($user->role, ['admin_kampus', 'organisasi', 'dosen'], true), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(EventRegistration::STATUSES))],
        ]);

        $registration = EventRegistration::where('campus_id', $user->campus_id)
            ->with('event')
            ->findOrFail($id);

        abort_unless(
            $user->role === 'admin_kampus' || $registration->event->organizer_id === $user->id,
            403,
            'Anda hanya dapat mengelola peserta kegiatan milik Anda.'
        );

        $registration->update(['status' => $validated['status']]);

        return back()->with('success', 'Status peserta diperbarui.');
    }
}
