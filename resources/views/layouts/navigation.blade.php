<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>                    
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Admin -->
                    @role('admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                        <x-nav-link :href="route('admin.guru.index')" :active="request()->routeIs('admin.guru.*')">Data Guru</x-nav-link>
                        <x-nav-link :href="route('admin.mapel.index')" :active="request()->routeIs('admin.mapel.*')">Mata Pelajaran</x-nav-link>
                        <x-nav-link :href="route('admin.role.index')" :active="request()->routeIs('admin.role.*')">
                            {{ __('Kelola Role') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('User & Role') }}
                        </x-nav-link>
                    @endrole

                    @php
                        $user = auth()->user();
                        $userRoles = $user->getRoleNames();
                        // Ambil role aktif dari session, default ke role pertama jika belum disentuh
                        $activeRole = session('active_role', $userRoles->first() ?? 'user');
                    @endphp

                    <!-- MENU JIKA MODE GURU ACTIVE -->
                    @if ($activeRole === 'guru')
                        <x-nav-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.dashboard')">
                            {{ __('Dashboard Guru') }}
                        </x-nav-link>

                        <x-nav-link :href="route('guru.absensi.index')" :active="request()->routeIs('guru.absensi.*')">
                            {{ __('Absensi Mapel') }}
                        </x-nav-link>
                    @endif

                    <!-- MENU JIKA MODE WALI KELAS ACTIVE -->
                    @if ($activeRole === 'walikelas')
                        <x-nav-link :href="route('walikelas.dashboard')" :active="request()->routeIs('walikelas.dashboard')">
                            {{ __('Dashboard Wali Kelas') }}
                        </x-nav-link>

                        <x-nav-link :href="route('walikelas.absensi.index')" :active="request()->routeIs('walikelas.absensi.*')">
                            {{ __('Absensi Harian') }}
                        </x-nav-link>
                    @endif

                </div>
                
            </div>

            <!-- Settings Dropdown -->
            <!-- Role Switcher & User Profile Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                
                @php
                    $user = auth()->user();
                    $userRoles = $user->getRoleNames(); // Mengambil semua role milik user
                    $activeRole = session('active_role', $userRoles->first() ?? 'user');
                @endphp

                <!-- Jika User Memiliki Lebih dari 1 Role, Tampilkan Dropdown Switcher Mode -->
                @if ($userRoles->count() > 1)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-xs font-bold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                                <svg class="w-3.5 h-3.5 me-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Mode: {{ strtoupper($activeRole) }}</span>
                                <svg class="ms-1.5 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                Pilih Mode Akses
                            </div>

                            @foreach ($userRoles as $role)
                                <form method="POST" action="{{ route('role.switch') }}">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $role }}">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold flex items-center justify-between transition-colors {{ $activeRole === $role ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <span>Mode {{ ucfirst($role) }}</span>
                                        @if ($activeRole === $role)
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Lencana Badge Jika Hanya Punya 1 Role -->
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl text-gray-600 bg-gray-100 border border-gray-200">
                        {{ strtoupper($activeRole) }}
                    </span>
                @endif

                <!-- Dropdown Profil User Asli (Breeze) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 mb-2">{{ Auth::user()->email }}</div>

                <!-- ========================================================= -->
                <!-- [TAMBAHAN] DROPDOWN SWITCH ROLE (MOBILE)                   -->
                <!-- ========================================================= -->
                @if(Auth::user()->roles->count() > 1)
                    <div class="mt-2 mb-3">
                        <form action="{{ route('role.switch') }}" method="POST" id="switch-role-mobile-form">
                            @csrf
                            <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Mode Akses:</label>
                            <select name="role" onchange="document.getElementById('switch-role-mobile-form').submit()" 
                                class="w-full text-xs font-semibold rounded-md border-gray-300 bg-indigo-50 text-indigo-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                @foreach(Auth::user()->roles as $role)
                                    <option value="{{ $role->name }}" {{ session('active_role', Auth::user()->roles->first()->name) == $role->name ? 'selected' : '' }}>
                                        Mode: {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
                <!-- ========================================================= -->
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>