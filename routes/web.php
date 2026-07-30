<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\AbsensiMapelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Siswa\PortalController;
use App\Http\Controllers\WaliKelas\AbsensiController as WaliAbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\JadwalPelajaranController;
use App\Http\Controllers\Admin\JamPelajaranController;


// Redirect Halaman Utama ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->post('/switch-role', [RoleSwitchController::class, 'switch'])->name('role.switch');


Route::get('/dashboard', function () {
    $user = auth()->user();

    // Jika session active_role belum ada, tentukan default dari role pertama user
    if (!session()->has('active_role')) {
        $defaultRole = $user->getRoleNames()->first() ?? 'siswa';
        session(['active_role' => $defaultRole]);
    }

    $activeRole = session('active_role');

    return match ($activeRole) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'walikelas' => redirect()->route('walikelas.dashboard'),
        'guru_bk' => redirect()->route('gurubk.dashboard'),
        'kepala_sekolah' => redirect()->route('kepsek.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ====================================================
// ROUTING BERDASARKAN ROLE (SPATIE RBAC)
// ====================================================

// 1. ROLE: ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::resource('guru', GuruController::class);
    Route::resource('mapel', MataPelajaranController::class);
    Route::resource('role', RoleController::class);
    // Kelola User & Role
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::post('/roles', [UserController::class, 'storeRole'])->name('roles.store');
    // TAMBAHKAN ROUTE SISWA DI SINI
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    // Route Jadwal Pelajaran
    Route::resource('jam-pelajaran', JamPelajaranController::class);
    Route::get('jadwal', [JadwalPelajaranController::class, 'index'])->name('jadwal.index');
    Route::post('jadwal', [JadwalPelajaranController::class, 'store'])->name('jadwal.store');
    Route::delete('jadwal/{jadwal}', [JadwalPelajaranController::class, 'destroy'])->name('jadwal.destroy');
    
});

// 2. ROLE: GURU MAPEL
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');
    Route::get('/absensi', [AbsensiMapelController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiMapelController::class, 'store'])->name('absensi.store');
});

// 3. ROLE: WALI KELAS
Route::middleware(['auth', 'role:walikelas'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'walikelas'])->name('dashboard');
    Route::get('/absensi', [WaliAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [WaliAbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/pdf', [WaliAbsensiController::class, 'exportPdf'])->name('absensi.pdf');
});

// 4. ROLE: GURU BK
Route::middleware(['auth', 'role:guru_bk'])->prefix('gurubk')->name('gurubk.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'gurubk'])->name('dashboard');
    Route::resource('pelanggaran', PelanggaranController::class);
});

// 5. ROLE: KEPALA SEKOLAH
Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kepsek'])->name('dashboard');
});

// 6. ROLE: SISWA
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';