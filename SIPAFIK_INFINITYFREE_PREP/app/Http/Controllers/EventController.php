<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    /** Role yang boleh membuat & mengelola kegiatan. */
    private const MANAGER_ROLES = ['admin_kampus', 'organisasi', 'dosen'];

    private function user()
    {
        return auth()->user();
    }

    private function activeCount(): \Closure
    {
        return fn ($q) => $q->whereIn('status', ['registered', 'attended']);
    }

    /** Apakah user boleh mengubah kegiatan ini? Admin kampus: semua di kampusnya. Organisasi/dosen: miliknya. */
    private function canManage(Event $event): bool
    {
        $user = $this->user();

        if ($event->campus_id !== $user->campus_id) {
            return false;
        }

        if ($user->role === 'admin_kampus') {
            return true;
        }

        return in_array($user->role, ['organisasi', 'dosen'], true)
            && $event->organizer_id === $user->id;
    }

    /** Mengambil kegiatan yang BOLEH dilihat user; selain itu 404 (agar keberadaan data kampus lain tidak bocor). */
    private function findVisible(int $id): Event
    {
        $event = Event::visibleTo($this->user())->with(['campus', 'organizer'])->findOrFail($id);

        // Kegiatan berstatus draf hanya terlihat oleh pengelolanya.
        if (! $this->canManage($event) && $event->status === 'draft') {
            abort(404);
        }

        return $event;
    }

    private function rules(bool $creating): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(array_keys(Event::CATEGORIES))],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => $creating
                ? ['required', 'date', 'after_or_equal:today']
                : ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'quota' => ['required', 'integer', 'min:1', 'max:100000'],
            'visibility' => ['required', Rule::in(['campus', 'public'])],
            'status' => ['required', Rule::in($creating ? ['draft', 'published'] : array_keys(Event::STATUSES))],
        ];
    }

    public function index(Request $request)
    {
        $user = $this->user();
        $isManager = in_array($user->role, self::MANAGER_ROLES, true);

        $query = Event::visibleTo($user)
            ->with(['campus', 'organizer'])
            ->withCount(['registrations as active_registrations_count' => $this->activeCount()]);

        // Tab: mine (kegiatan saya), campus (semua kegiatan kampus), public (kampus lain)
        $scope = $request->query('scope');

        if ($user->role === 'mahasiswa') {
            $scope = in_array($scope, ['campus', 'public'], true) ? $scope : 'campus';
            $query->where('status', 'published');
        } elseif ($user->role === 'admin_kampus') {
            $scope = in_array($scope, ['campus', 'public'], true) ? $scope : 'campus';
        } else {
            $scope = in_array($scope, ['mine', 'campus', 'public'], true) ? $scope : 'mine';
        }

        match ($scope) {
            'mine' => $query->where('organizer_id', $user->id),
            'public' => $query->where('campus_id', '!=', $user->campus_id),
            default => $query->where('campus_id', $user->campus_id),
        };

        // Selain pengelola, kegiatan kampus sendiri yang draf/batal tidak ditampilkan.
        if ($scope !== 'mine' && $user->role !== 'admin_kampus') {
            $query->whereIn('status', ['published', 'completed']);
        }

        if ($request->filled('q')) {
            $keyword = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category') && array_key_exists($request->query('category'), Event::CATEGORIES)) {
            $query->where('category', $request->query('category'));
        }

        if ($request->query('time') === 'past') {
            $query->whereDate('event_date', '<', today())->orderByDesc('event_date');
        } else {
            $query->whereDate('event_date', '>=', today())->orderBy('event_date')->orderBy('start_time');
        }

        $events = $query->paginate(9)->withQueryString();

        $myRegistrations = $user->role === 'mahasiswa'
            ? EventRegistration::where('user_id', $user->id)
                ->whereIn('status', ['registered', 'attended'])
                ->pluck('event_id')->all()
            : [];

        return view('events.index', [
            'events' => $events,
            'scope' => $scope,
            'isManager' => $isManager,
            'myRegistrations' => $myRegistrations,
            'categories' => Event::CATEGORIES,
        ]);
    }

    /** Jadwal kegiatan: urutan waktu kegiatan mendatang yang dapat dilihat user. */
    public function schedule()
    {
        $user = $this->user();

        $query = Event::visibleTo($user)
            ->with(['campus'])
            ->withCount(['registrations as active_registrations_count' => $this->activeCount()])
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')->orderBy('start_time');

        if ($user->role === 'mahasiswa' || $user->role === 'admin_kampus') {
            $query->where('status', 'published');
        } else {
            // organisasi & dosen: kegiatan miliknya (semua status aktif) + kegiatan dibuka lain
            $query->where(function ($q) use ($user) {
                $q->where('organizer_id', $user->id)->orWhere('status', 'published');
            })->whereIn('status', ['draft', 'published']);
        }

        $events = $query->limit(60)->get();

        $myRegistrations = $user->role === 'mahasiswa'
            ? EventRegistration::where('user_id', $user->id)
                ->whereIn('status', ['registered', 'attended'])
                ->pluck('event_id')->all()
            : [];

        return view('events.schedule', [
            'grouped' => $events->groupBy(fn ($e) => $e->event_date->toDateString()),
            'myRegistrations' => $myRegistrations,
        ]);
    }

    public function create()
    {
        abort_unless(in_array($this->user()->role, self::MANAGER_ROLES, true), 403);

        return view('events.form', ['categories' => Event::CATEGORIES]);
    }

    public function store(Request $request)
    {
        abort_unless(in_array($this->user()->role, self::MANAGER_ROLES, true), 403);

        $validated = $request->validate($this->rules(true));

        $validated['campus_id'] = $this->user()->campus_id;
        $validated['organizer_id'] = $this->user()->id;

        $event = Event::create($validated);

        return redirect()
            ->route('campus.events.show', $event->id)
            ->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(int $id)
    {
        $user = $this->user();
        $event = $this->findVisible($id);

        $event->loadCount(['registrations as active_registrations_count' => $this->activeCount()]);

        $canManage = $this->canManage($event);

        $registrations = $canManage
            ? $event->registrations()->with('user.campus')->orderBy('created_at')->get()
            : collect();

        $myRegistration = $user->role === 'mahasiswa'
            ? EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first()
            : null;

        return view('events.show', compact('event', 'canManage', 'registrations', 'myRegistration'));
    }

    public function edit(int $id)
    {
        $event = Event::where('campus_id', $this->user()->campus_id)->findOrFail($id);
        abort_unless($this->canManage($event), 403, 'Anda hanya dapat mengubah kegiatan milik Anda.');

        return view('events.form', [
            'event' => $event,
            'categories' => Event::CATEGORIES,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $event = Event::where('campus_id', $this->user()->campus_id)->findOrFail($id);
        abort_unless($this->canManage($event), 403, 'Anda hanya dapat mengubah kegiatan milik Anda.');

        $validated = $request->validate($this->rules(false));

        $used = $event->activeRegistrations()->count();
        if ($validated['quota'] < $used) {
            throw ValidationException::withMessages([
                'quota' => "Kuota tidak boleh lebih kecil dari jumlah peserta aktif ({$used}).",
            ]);
        }

        $event->update($validated);

        return redirect()
            ->route('campus.events.show', $event->id)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function register(int $id)
    {
        $user = $this->user();
        abort_unless($user->role === 'mahasiswa', 403, 'Hanya mahasiswa yang dapat mendaftar kegiatan.');

        $event = $this->findVisible($id);

        try {
            DB::transaction(function () use ($event, $user) {
                // Kunci baris kegiatan agar dua pendaftar bersamaan tidak melewati kuota.
                $locked = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== 'published') {
                    throw ValidationException::withMessages(['event' => 'Pendaftaran untuk kegiatan ini sedang tidak dibuka.']);
                }

                if ($locked->event_date->isBefore(today())) {
                    throw ValidationException::withMessages(['event' => 'Kegiatan ini sudah berlalu.']);
                }

                $existing = EventRegistration::where('event_id', $locked->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing && in_array($existing->status, ['registered', 'attended'], true)) {
                    throw ValidationException::withMessages(['event' => 'Anda sudah terdaftar pada kegiatan ini.']);
                }

                $used = $locked->activeRegistrations()->count();
                if ($used >= $locked->quota) {
                    throw ValidationException::withMessages(['event' => 'Kuota peserta sudah penuh.']);
                }

                if ($existing) {
                    $existing->update(['status' => 'registered', 'registered_at' => now()]);
                } else {
                    EventRegistration::create([
                        'event_id' => $locked->id,
                        'user_id' => $user->id,
                        'campus_id' => $locked->campus_id,
                        'status' => 'registered',
                        'registered_at' => now(),
                    ]);
                }
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('campus.events.show', $event->id)
            ->with('success', 'Pendaftaran berhasil. Sampai jumpa di kegiatan!');
    }

    public function unregister(int $id)
    {
        $user = $this->user();
        abort_unless($user->role === 'mahasiswa', 403);

        $registration = EventRegistration::where('event_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->with('event')
            ->firstOrFail();

        if ($registration->event->event_date->isBefore(today())) {
            return back()->with('error', 'Kegiatan yang sudah berlalu tidak dapat dibatalkan.');
        }

        $registration->update(['status' => 'cancelled']);

        return back()->with('success', 'Pendaftaran Anda dibatalkan.');
    }
}
