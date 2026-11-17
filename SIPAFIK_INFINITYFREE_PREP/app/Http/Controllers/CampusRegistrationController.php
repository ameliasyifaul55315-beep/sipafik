<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CampusRegistrationController extends Controller
{
    public function create()
    {
        return view('campus.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'alpha_dash',
                'min:3',
                'max:30',
                'unique:campuses,username',
                'unique:users,username',
            ],
            'email' => [
                'required',
                'email',
                'unique:campuses,email',
                'unique:users,email',
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'responsible_person' => 'required|string|max:255',
            'identity_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request, $validated) {

            $documentPath = $request
                ->file('identity_document')
                ->store('campus-documents', 'public');

            $campus = Campus::create([
                'name' => $validated['name'],
                'username' => strtolower($validated['username']),
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'],
                'responsible_person' => $validated['responsible_person'],
                'identity_document' => $documentPath,
                'status' => 'pending',
            ]);

            User::create([
                'name' => $validated['responsible_person'],
                'email' => $validated['email'],
                'username' => strtolower($validated['username']),
                'password' => Hash::make($validated['password']),
                'campus_id' => $campus->id,
                'role' => 'admin_kampus',
                'is_active' => false,
            ]);
        });

        return redirect()
            ->route('campus.register')
            ->with('success', 'Pendaftaran kampus berhasil dikirim. Silakan menunggu verifikasi Super Admin.');
    }
}
