<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan pengguna oleh Admin Kampus. Semua query dibatasi pada kampus milik Admin Kampus yang login.
 */
class CampusUserController extends Controller
{
    /** Role yang boleh dibuat/dikelola oleh Admin Kampus. */
    public const MANAGED_ROLES = [
        'dosen' => 'Dosen',
        'organisasi' => 'Organisasi',
        'petugas_fasilitas' => 'Petugas Fasilitas',
        'mahasiswa' => 'Mahasiswa',
    ];

    private function guard(): void
    {
        abort_unless(auth()->user()?->role === 'admin_kampus', 403, 'Hanya Admin Kampus yang dapat mengelola pengguna.');
    }

    private function campusUsers()
    {
        return User::where('campus_id', auth()->user()->campus_id);
    }

    public function index(Request $request)
    {
        $this->guard();

        $query = $this->campusUsers()->orderBy('role')->orderBy('name');

        if ($request->filled('q')) {
            $keyword = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if (in_array($request->query('status'), ['active', 'inactive'], true)) {
            $query->where('is_active', $request->query('status') === 'active');
        }

        $users = $query->paginate(12)->withQueryString();

        $counts = $this->campusUsers()->selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('campus-users.index', [
            'users' => $users,
            'counts' => $counts,
            'roles' => self::MANAGED_ROLES,
        ]);
    }

    public function create()
    {
        $this->guard();

        return view('campus-users.form', ['roles' => self::MANAGED_ROLES]);
    }

    public function store(Request $request)
    {
        $this->guard();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:30', 'unique:users,username'],
            'role' => ['required', Rule::in(array_keys(self::MANAGED_ROLES))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => strtolower($validated['username']),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'campus_id' => auth()->user()->campus_id,
            'is_active' => true,
        ]);

        return redirect()
            ->route('campus.users.index')
            ->with('success', 'Akun '.self::MANAGED_ROLES[$validated['role']].' berhasil dibuat.');
    }

    public function toggleStatus(int $id)
    {
        $this->guard();

        $user = $this->campusUsers()->findOrFail($id);

        abort_unless(
            array_key_exists($user->role, self::MANAGED_ROLES),
            403,
            'Akun ini tidak dapat diubah oleh Admin Kampus.'
        );

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}
