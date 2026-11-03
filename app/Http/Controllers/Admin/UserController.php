<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Daftar semua akun pengguna (difilter per role).
     */
    public function index(Request $request): View
    {
        $role = $request->query('role');

        $users = User::query()
            ->when($role, fn ($q) => $q->where('role', $role))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'admin' => User::where('role', 'admin')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'total' => User::count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'role'));
    }

    /**
     * Form buat akun baru (default role guru).
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,guru,siswa'],
            'position' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Akun '.$validated['name'].' ('.$validated['role'].') berhasil dibuat.');
    }

    /**
     * Form edit akun.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update akun (nama, email, role, jabatan, NIP, foto; password opsional).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Jangan izinkan admin menghapus/menurunkan role dirinya sendiri
        $isSelf = $user->is(auth()->user());

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:admin,guru,siswa'],
            'position' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($isSelf && $validated['role'] !== 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat mengubah role akun sendiri dari admin.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position' => $validated['position'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && ! str_starts_with($user->photo, 'http')) {
                Storage::disk('public')->delete($user->photo);
            }
            $payload['photo'] = $request->file('photo')->store('users', 'public');
        } elseif ($request->boolean('remove_photo')) {
            if ($user->photo && ! str_starts_with($user->photo, 'http')) {
                Storage::disk('public')->delete($user->photo);
            }
            $payload['photo'] = null;
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')->with('status', 'Akun '.$user->name.' berhasil diperbarui.');
    }

    /**
     * Hapus akun (tidak boleh menghapus diri sendiri / admin terakhir).
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')->with('error', 'Admin terakhir tidak dapat dihapus (tetapkan admin lain terlebih dahulu).');
        }

        if ($user->photo && ! str_starts_with($user->photo, 'http')) {
            Storage::disk('public')->delete($user->photo);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Akun '.$name.' berhasil dihapus.');
    }
}
