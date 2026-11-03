<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Foto Profil -->
        <div class="flex items-center gap-5 p-4 bg-gray-50 rounded-xl border border-gray-200">
            @if ($user->photo_url)
                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover border-2 border-white shadow">
            @else
                <div class="h-20 w-20 rounded-full bg-slate-800 text-white flex items-center justify-center text-xl font-bold border-2 border-white shadow">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <div class="flex-1">
                <x-input-label for="photo" value="Foto Profil / Pas Foto" />
                <input id="photo" name="photo" type="file" accept="image/*"
                       class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                <p class="mt-1 text-[11px] text-gray-500">Format gambar JPG, PNG, atau WEBP (maks. 2 MB).</p>
                @if ($user->photo_url)
                    <label class="mt-2 inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-800">
                        <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span>Hapus foto saat ini</span>
                    </label>
                @endif
                <x-input-error class="mt-1" :messages="$errors->get('photo')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        @if ($user->position)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="position" value="Jabatan / Tugas" />
                    <input id="position" type="text" disabled value="{{ $user->position }}"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-600 shadow-sm text-sm">
                    <p class="mt-1 text-[11px] text-gray-400">Jabatan diatur oleh Administrator sekolah.</p>
                </div>

                <div>
                    <x-input-label for="nip" value="NIP (Nomor Induk Pegawai)" />
                    <x-text-input id="nip" name="nip" type="text" class="mt-1 block w-full" :value="old('nip', $user->nip)" placeholder="Opsional" />
                    <x-input-error class="mt-2" :messages="$errors->get('nip')" />
                </div>
            </div>
        @endif

        @if ($user->role === 'guru')
            <div>
                <x-input-label for="bio" value="Tentang Guru (tampil di halaman pribadi publik)" />
                <textarea id="bio" name="bio" rows="5" maxlength="1000"
                          placeholder="Tulis profil singkat Anda: latar belakang, mata pelajaran/keahlian, dan komentar pembuka untuk pengunjung halaman pribadi Anda..."
                          class="mt-1 block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('bio', $user->bio) }}</textarea>
                <p class="mt-1 text-[11px] text-gray-400">Tampil di halaman pribadi Anda (section "Tentang Guru"). NIP tidak pernah tampil publik.</p>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>
        @endif

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if ($user->role === 'guru')
            @include('profile.partials.social-links-form')
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
