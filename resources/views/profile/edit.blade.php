@php
    // Layout mengikuti role agar sidebar dashboard tetap tampil di /profile
    $profileLayout = match (auth()->user()?->role) {
        'admin' => 'admin-layout',
        'guru' => 'guru-layout',
        'siswa' => 'siswa-layout',
        default => 'app-layout',
    };
@endphp

<x-dynamic-component :component="$profileLayout">
    <div class="max-w-4xl mx-auto space-y-6 py-2">
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-gray-900">Profil Akun</h2>
            <p class="text-sm text-gray-600 mt-1">Perbarui data profil, foto, dan kata sandi akun Anda.</p>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-200">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-200">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-200">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dynamic-component>
