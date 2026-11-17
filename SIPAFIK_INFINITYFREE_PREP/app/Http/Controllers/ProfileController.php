<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->loadMissing('campus');

        // Super Admin memakai layout dashboard tersendiri.
        if ($user->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        return view('profile.show', ['user' => $user, 'campus' => $user->campus]);
    }
}
