<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan pengguna: (1) memiliki salah satu role yang diizinkan, (2) akunnya aktif,
 * (3) terhubung ke kampus yang sudah DISETUJUI Super Admin.
 * Pemakaian: ->middleware('campus.access:admin_kampus,petugas_fasilitas')
 */
class EnsureCampusAccess
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if ($roles !== []) {
            abort_unless(in_array($user->role, $roles, true), 403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        abort_unless($user->is_active, 403, 'Akun Anda sedang tidak aktif. Hubungi Admin Kampus.');
        abort_unless($user->campus_id, 403, 'Akun Anda belum terhubung ke kampus.');

        $user->loadMissing('campus');

        abort_unless(
            $user->campus && $user->campus->status === 'approved',
            403,
            'Kampus Anda belum disetujui oleh Super Admin.'
        );

        return $next($request);
    }
}
