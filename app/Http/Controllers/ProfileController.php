<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? $user->nip,
            'bio' => $validated['bio'] ?? $user->bio,
        ]);

        // Social links (hanya untuk guru)
        if ($user->role === 'guru') {
            $social = $request->input('social_links', []);
            $clean = [];
            // predefined
            $predefined = ['facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'github', 'website'];
            foreach ($predefined as $key) {
                $val = trim((string) ($social[$key] ?? ''));
                if ($val !== '') {
                    $clean[$key] = $val;
                }
            }
            // custom links array
            if (! empty($social['custom']) && is_array($social['custom'])) {
                $customClean = [];
                foreach ($social['custom'] as $c) {
                    $label = trim((string) ($c['label'] ?? ''));
                    $url = trim((string) ($c['url'] ?? ''));
                    if ($label !== '' && $url !== '') {
                        $customClean[] = ['label' => $label, 'url' => $url];
                    }
                }
                if ($customClean) {
                    $clean['custom'] = $customClean;
                }
            }
            $user->social_links = $clean ? $clean : null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && ! str_starts_with($user->photo, 'http')) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users', 'public');
        } elseif ($request->boolean('remove_photo')) {
            if ($user->photo && ! str_starts_with($user->photo, 'http')) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
