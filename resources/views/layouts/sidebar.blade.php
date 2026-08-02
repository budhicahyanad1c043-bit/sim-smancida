@php
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $activeRole = session('active_role', $userRoles->first() ?? 'user');
@endphp

<!-- Mobile Backdrop (Diperbarui dengan x-cloak) -->
<div x-cloak
     x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"></div>

<!-- Sidebar Aside (Diperbarui dengan x-cloak) -->
<aside x-cloak
       class="fixed top-0 bottom-0 left-0 z-40 flex flex-col bg-white border-r border-gray-100 transition-all duration-300 ease-in-out shadow-xs"
       :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
           sidebarCollapsed ? 'lg:w-20' : 'lg:w-64',
           'w-64'
       ]">

    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-lg flex-shrink-0 shadow-md shadow-indigo-100">
                S
            </div>
            <span x-show="!sidebarCollapsed" 
                  class="font-black text-gray-800 text-lg tracking-wider whitespace-nowrap">
                SIAKAD
            </span>
        </a>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Nav Links Container -->
    <div class="flex-1 overflow-y-auto p-3 space-y-1.5">
        
        <div class="px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap"
             :class="sidebarCollapsed ? 'lg:text-center lg:px-0' : ''">
            <span x-show="!sidebarCollapsed">Navigasi Utama</span>
            <span x-show="sidebarCollapsed" class="hidden lg:inline">•</span>
        </div>

        <!-- ================= MODE ADMIN ================= -->
        @if ($activeRole === 'admin')
            <a href="{{ route('admin.dashboard') }}" 
               title="Dashboard Admin"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- MENU DATA GURU -->
            <a href="{{ route('admin.guru.index') }}" 
               title="Data Guru"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.guru.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Data Guru</span>
            </a>

            <!-- MENU DATA SISWA -->
            <a href="{{ route('admin.siswa.index') }}" 
            title="Data Siswa"
            class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.siswa.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
            </svg> 
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Data Siswa</span>
            </a>

            <!-- MENU KELOLA KELAS -->
            <a href="{{ route('admin.kelas.index') }}" 
            title="Kelola Kelas"
            class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.kelas.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0a2 2 0 012-2h2a2 2 0 012 2m-6 0v-4a2 2 0 012-2h2a2 2 0 012 2v4"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Kelola Kelas</span>
            </a>

            <!-- MENU MATA PELAJARAN -->
            <a href="{{ route('admin.mapel.index') }}" 
               title="Mata Pelajaran"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.mapel.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Mata Pelajaran</span>
            </a>

            <!-- MENU JAM PELAJARAN (BARU DITAMBAHKAN) -->
            <a href="{{ route('admin.jam-pelajaran.index') }}" 
               title="Jam Mengajar / Alokasi Jam"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.jam-pelajaran.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Jam Pelajaran</span>
            </a>

            <!-- MENU JADWAL PELAJARAN (BARU DITAMBAHKAN) -->
            <a href="{{ route('admin.jadwal.index') }}" 
               title="Jadwal Pelajaran"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.jadwal.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Jadwal Pelajaran</span>
            </a>

            <!-- MENU KELOLA ROLE -->
            <a href="{{ route('admin.role.index') }}" 
               title="Kelola Role"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.role.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Kelola Role</span>
            </a>

            <!-- MENU USER & ROLE -->
            <a href="{{ route('admin.users.index') }}" 
               title="User & Role"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                                       
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">User & Role</span>
            </a>

            <!-- MENU ATUR LOKASI ABSENSI -->
            <a href="{{ route('admin.lokasi.index') }}" 
            title="Atur Lokasi Absen"
            class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('admin.lokasi.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Atur Lokasi Absen</span>
            </a>

        @endif

        <!-- ================= MODE GURU ================= -->
        @if ($activeRole === 'guru')

            <!-- DASHBOARD -->
            <a href="{{ route('guru.dashboard') }}" 
               title="Dashboard Guru"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('guru.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard Guru</span>
            </a>

            <!-- JADWAL PELAJARAN (BARU DITAMBAHKAN) -->
            <a href="{{ route('guru.jadwal.index') }}" 
               title="Jadwal Mengajar"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('guru.jadwal.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Jadwal Mengajar</span>
            </a>
            
            <a href="{{ route('guru.absensi.index') }}" 
               title="Absensi Mapel"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('guru.absensi.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Absensi Mapel</span>
            </a>

        @endif

        <!-- ================= MODE KEPALA SEKOLAH ================= -->
        @if (in_array($activeRole, ['kepala_sekolah', 'kepalasekolah', 'kepala-sekolah']))
            <a href="{{ route('kepsek.dashboard') }}" 
            title="Monitoring Realtime Guru"
            class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('kepsek.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
            :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Monitoring Realtime</span>
            </a>
        @endif

        <!-- ================= MODE WALI KELAS ================= -->
        @if ($activeRole === 'walikelas')
            <a href="{{ route('walikelas.dashboard') }}" 
               title="Dashboard Wali Kelas"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('walikelas.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard Wali Kelas</span>
            </a>

            <a href="{{ route('walikelas.absensi.index') }}" 
               title="Absensi Harian"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('walikelas.absensi.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Absensi Harian</span>
            </a>
        @endif

        <!-- ================= MODE SISWA ================= -->
        @if ($activeRole === 'siswa')

            <!-- DASHBOARD -->
            <a href="{{ route('siswa.dashboard') }}" 
               title="Dashboard Siswa"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('siswa.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- JADWAL PELAJARAN (BARU DITAMBAHKAN) -->
            <a href="{{ route('siswa.jadwal.index') }}" 
               title="Jadwal Pelajaran"
               class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('siswa.jadwal.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}"
               :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Jadwal Pelajaran</span>
            </a>

        @endif

    </div>

    <!-- Footer Profile Indicator -->
    <div class="p-3 border-t border-gray-100 bg-gray-50/50 flex-shrink-0">
        <div class="flex items-center gap-3 px-2 py-1.5" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs flex-shrink-0"
                 title="Role Aktif: {{ strtoupper($activeRole) }}">
                {{ strtoupper(substr($activeRole, 0, 1)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="overflow-hidden">
                <p class="text-xs font-bold text-gray-800 truncate">{{ $user->name }}</p>
                <p class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wider">{{ $activeRole }}</p>
            </div>
        </div>
    </div>

</aside>