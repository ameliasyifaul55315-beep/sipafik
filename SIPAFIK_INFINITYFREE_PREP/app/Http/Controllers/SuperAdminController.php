<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function loginForm()
    {
        return view('super-admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        if (Auth::user()->role !== 'super_admin') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun ini bukan akun Super Admin.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('super-admin.dashboard');
    }

    public function dashboard()
    {
        $this->checkSuperAdmin();

        // Data seluruh kampus
        $campuses = Campus::latest()->get();

        // Statistik utama
        $stats = [
            'total_users' => User::count(),

            'total_campuses' => Campus::count(),

            'pending_campuses' => Campus::where('status', 'pending')->count(),

            'approved_campuses' => Campus::where('status', 'approved')->count(),

            'rejected_campuses' => Campus::where('status', 'rejected')->count(),
        ];

        // Distribusi pengguna berdasarkan role
        $roleStats = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->orderByDesc('total')
            ->get();

        // Pengguna terbaru
        $recentUsers = User::latest()
            ->take(6)
            ->get();

        // Pengawasan aktivitas seluruh sistem
        $activity = [
            'totals' => [
                'Kegiatan' => \App\Models\Event::count(),
                'Pendaftaran Peserta' => \App\Models\EventRegistration::count(),
                'Laboratorium' => \App\Models\Laboratory::count(),
                'Jadwal Lab' => \App\Models\LabSchedule::count(),
                'Permohonan Fasilitas' => \App\Models\FacilityRequest::count(),
            ],
            'per_campus' => Campus::withCount([
                'users', 'events', 'laboratories', 'facilityRequests',
                'facilityRequests as pending_requests_count' => fn ($q) => $q->where('status', 'pending'),
            ])->orderBy('name')->get(),
        ];

        return view('super-admin.dashboard', compact(
            'campuses',
            'stats',
            'roleStats',
            'recentUsers',
            'activity'
        ));
    }

    public function approve($id)
    {
        $this->checkSuperAdmin();

        $campus = Campus::findOrFail($id);

        if ($campus->status !== 'pending') {
            return back()->with('error', 'Pendaftaran kampus sudah diproses.');
        }

        DB::transaction(function () use ($campus) {
            $campus->update([
                'status' => 'approved',
                'rejection_reason' => null,
            ]);

            User::where('campus_id', $campus->id)
                ->where('role', 'admin_kampus')
                ->update(['is_active' => true]);
        });

        return back()->with('success', 'Kampus berhasil disetujui dan akun Admin Kampus diaktifkan.');
    }

    public function reject(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $campus = Campus::findOrFail($id);

        if ($campus->status !== 'pending') {
            return back()->with('error', 'Pendaftaran kampus sudah diproses.');
        }

        DB::transaction(function () use ($campus, $request) {
            $campus->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            User::where('campus_id', $campus->id)
                ->where('role', 'admin_kampus')
                ->update(['is_active' => false]);
        });

        return back()->with('success', 'Pendaftaran kampus berhasil ditolak.');
    }

    public function users(Request $request)
    {
        $this->checkSuperAdmin();

        $query = User::with('campus')->latest();

        // Pencarian nama, username, atau email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter kampus
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }

        // Filter status akun
        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $users = $query->paginate(12)->withQueryString();

        $campuses = Campus::orderBy('name')->get();

        $roles = [
            'super_admin' => 'Super Admin',
            'admin_kampus' => 'Admin Kampus',
            'organisasi' => 'Organisasi',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
            'petugas_fasilitas' => 'Petugas Fasilitas',
            'admin' => 'Admin',
            'petugas_laboratorium' => 'Petugas Laboratorium',
        ];

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('super-admin.users', compact(
            'users',
            'campuses',
            'roles',
            'stats'
        ));
    }

    public function toggleUserStatus($id)
    {
        $this->checkSuperAdmin();

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with(
                'error',
                'Anda tidak dapat menonaktifkan akun sendiri.'
            );
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return back()->with(
            'success',
            $user->is_active
                ? 'Akun berhasil diaktifkan.'
                : 'Akun berhasil dinonaktifkan.'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }

    private function checkSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'super_admin',
            403,
            'Akses hanya untuk Super Admin.'
        );
    }
}