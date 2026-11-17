<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaboratoryController extends Controller
{
    private function checkAccess(bool $manage = false): void
    {
        $user = auth()->user();

        abort_unless(
            in_array($user->role, ['admin_kampus', 'petugas_fasilitas']),
            403
        );

        abort_unless($user->campus_id, 403);

        $user->loadMissing('campus');

        abort_unless(
            $user->campus && $user->campus->status === 'approved',
            403,
            'Kampus belum disetujui.'
        );
    }

    private function campusLaboratories()
    {
        return Laboratory::where(
            'campus_id',
            auth()->user()->campus_id
        );
    }

    public function index()
    {
        $this->checkAccess();

        $laboratories = $this->campusLaboratories()
            ->latest()
            ->paginate(10);

        return view('laboratories.index', compact('laboratories'));
    }

    public function create()
    {
        $this->checkAccess(true);

        return view('laboratories.form');
    }

    public function store(Request $request)
    {
        $this->checkAccess(true);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:laboratories,code'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'facilities' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['campus_id'] = auth()->user()->campus_id;
        $validated['facilities'] = collect(
            preg_split('/[,;\n]+/', $validated['facilities'] ?? '')
        )->map(fn ($item) => trim($item))
         ->filter()
         ->values()
         ->all();

        Laboratory::create($validated);

        return redirect()
            ->route('campus.laboratories.index')
            ->with('success', 'Laboratorium berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $this->checkAccess(true);

        $laboratory = $this->campusLaboratories()->findOrFail($id);

        return view('laboratories.form', compact('laboratory'));
    }

    public function update(Request $request, int $id)
    {
        $this->checkAccess(true);

        $laboratory = $this->campusLaboratories()->findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('laboratories', 'code')->ignore($laboratory->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'facilities' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['facilities'] = collect(
            preg_split('/[,;\n]+/', $validated['facilities'] ?? '')
        )->map(fn ($item) => trim($item))
         ->filter()
         ->values()
         ->all();

        $laboratory->update($validated);

        return redirect()
            ->route('campus.laboratories.index')
            ->with('success', 'Data laboratorium berhasil diperbarui.');
    }

    public function toggleStatus(int $id)
    {
        $this->checkAccess(true);

        $laboratory = $this->campusLaboratories()->findOrFail($id);

        $laboratory->update([
            'status' => $laboratory->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        return back()->with('success', 'Status laboratorium berhasil diubah.');
    }
}
