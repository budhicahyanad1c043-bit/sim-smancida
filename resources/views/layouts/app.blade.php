<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAKAD') }}</title>

    <!-- Anti Flicker CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full text-gray-900 bg-gray-50" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: false,
          isLoading: false
      }"
      x-init="
          window.addEventListener('beforeunload', () => { isLoading = true });
          document.querySelectorAll('form').forEach(f => {
              f.addEventListener('submit', () => { isLoading = true });
          });
          window.addEventListener('pageshow', (event) => {
              isLoading = false;
          });

          // Set loading saat berpindah halaman
          window.addEventListener('beforeunload', () => { 
              isLoading = true; 
          });

          // Set loading saat submit form
          document.querySelectorAll('form').forEach(f => {
              f.addEventListener('submit', () => { isLoading = true; });
          });
      ">

    <!-- GLOBAL LOADING OVERLAY -->
    <div x-cloak 
         x-show="isLoading" 
         x-transition:enter="transition opacity-0 ease-out duration-200"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity-100 ease-in duration-150"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-xs">
        <div class="bg-white p-5 rounded-2xl shadow-2xl flex items-center gap-4">
            <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-bold text-gray-700">Memproses Data...</span>
        </div>
    </div>

    <div class="min-h-screen bg-gray-50 flex">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
            
            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 shadow-xs">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex p-2 rounded-xl text-gray-500 hover:bg-gray-100">
                        <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>

                <!-- Top Right Dropdowns -->
                <div class="flex items-center gap-3">
                    @php
                        $user = auth()->user();
                        $userRoles = $user->getRoleNames();
                        $activeRole = session('active_role', $userRoles->first() ?? 'user');
                    @endphp

                    @if ($userRoles->count() > 1)
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-xs font-bold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition shadow-xs">
                                    <svg class="w-3.5 h-3.5 me-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Mode: {{ strtoupper($activeRole) }}</span>
                                    <svg class="ms-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="block px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Ganti Mode Akses</div>
                                @foreach ($userRoles as $role)
                                    <form method="POST" action="{{ route('role.switch') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $role }}">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold flex items-center justify-between {{ $activeRole === $role ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <span>Mode {{ ucfirst($role) }}</span>
                                            @if ($activeRole === $role)
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:text-gray-900 focus:outline-none">
                                
                                <!-- FOTO PROFIL ATAU INISIAL -->
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                        alt="{{ $user->name }}" 
                                        class="w-7 h-7 rounded-full object-cover border border-indigo-200">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif

                                <span class="hidden sm:inline-block truncate max-w-[150px]">{{ $user->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            @if (isset($header))
                <div class="bg-white border-b border-gray-100 py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </div>
            @endif

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>