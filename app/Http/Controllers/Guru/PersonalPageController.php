<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Kustomisasi halaman pribadi guru: headline, CV, prestasi.
 */
class PersonalPageController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = GuruProfile::query()->firstOrCreate(['user_id' => $request->user()->id]);

        return view('guru.pribadi.edit', [
            'guru' => $request->user(),
            'guruProfile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = GuruProfile::query()->firstOrCreate(['user_id' => $request->user()->id]);

        $validated = $request->validate([
            'headline' => ['nullable', 'string', 'max:150'],
            'cv_content' => ['nullable', 'string', 'max:10000'],
            'prestasi' => ['nullable', 'string', 'max:5000'],
        ]);

        $profile->fill([
            'headline' => $validated['headline'] ?? $profile->headline,
            'cv_content' => $validated['cv_content'] ?? $profile->cv_content,
            'prestasi' => $validated['prestasi'] ?? $profile->prestasi,
        ]);

        $profile->save();

        return redirect()->route('guru.pribadi.edit')->with('status', 'Halaman pribadi berhasil diperbarui.');
    }
}
