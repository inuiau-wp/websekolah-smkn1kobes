<x-guest-layout guestTitle="Masuk Akun">
    <!-- Heading -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang 👋</h2>
        <p class="text-sm text-gray-500 mt-1">Masuk untuk mengelola konten, berita, dan akademik sekolah.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-baseline justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800 hover:underline ml-2">Lupa kata sandi?</a>
                @endif
            </div>
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#1c398e] shadow-sm focus:ring-[#1c398e]" name="remember">
            <!-- Warna checkbox indigo smkn1kotabesi (TKJ default) -->
            Ingat saya
        </label>

        <div>
            <!-- Tombol masuk dengan aksen warna ATP/TKJ/AKL bergaris -->
            <button type="submit"
                    class="block w-full py-3 rounded-xl font-semibold text-white tracking-wide
                           bg-gray-900 hover:bg-gray-800
                           border-b-4 border-[#e17100] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition">
                Masuk ke Dashboard
            </button>
            <p class="text-[11px] text-center text-gray-400 mt-3">
                Pendaftaran akun baru dinonaktifkan.
            </p>
        </div>
    </form>
</x-guest-layout>
