<x-guest-layout>
    <div x-data="{ 
        showPassword: false, 
        isSubmitting: false 
    }" class="w-full">

        <!-- Overlay Loading Login -->
        <div x-cloak x-show="isSubmitting" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-xs">
            <div class="bg-white p-5 rounded-2xl shadow-xl flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-bold text-gray-700">Otentikasi Akun...</span>
            </div>
        </div>

        <!-- Header Form -->
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black text-xl shadow-lg shadow-indigo-200 mb-3">
                S
            </div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">SIAKAD Akses</h2>
            <p class="text-xs text-gray-500 mt-1">Masukkan kredensial Anda untuk masuk ke sistem.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" @submit="isSubmitting = true" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Alamat Email" class="text-xs font-bold text-gray-700" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                           placeholder="nama@sekolah.sch.id"
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition duration-150">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password with Eye Toggle -->
            <div>
                <x-input-label for="password" value="Kata Sandi" class="text-xs font-bold text-gray-700" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required 
                           placeholder="••••••••"
                           class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition duration-150">

                    <!-- Tombol Mata (Toggle Password) -->
                    <button type="button" @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-600 focus:outline-none">
                        <!-- Mata Terbuka -->
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <!-- Mata Tertutup -->
                        <svg x-cloak x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.813c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.168 5.478M9.88 9.88a3 3 0 104.243 4.243M3 3l18 18"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500">
                    <span class="ms-2 text-xs font-semibold text-gray-600">Ingat Saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full mt-2 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-100 transition duration-150">
                Masuk Sistem
            </button>
        </form>
    </div>
</x-guest-layout>