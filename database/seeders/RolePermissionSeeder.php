<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DAFTAR PERMISSION LENGKAP
        $permissions = [
            // Admin & Master Data
            'manage-users',
            'manage-roles',
            'manage-guru',
            'manage-siswa',
            'manage-mapel',
            'manage-kelas',

            // Guru Mapel
            'view-jadwal-mapel',
            'input-absensi-mapel',
            'view-absensi-mapel',

            // Wali Kelas
            'view-kelas-binaan',
            'input-absensi-harian',
            'view-absensi-harian',
            'cetak-rekap-absensi',

            // Guru BK
            'manage-catatan-pelanggaran',

            // Siswa
            'view-portal-siswa',
            'view-rekap-presensi-pribadi',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. ASSIGN PERMISSION KE MASING-MASING ROLE

        // Admin (Dapat semua akses)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // Guru Mapel
        $roleGuru = Role::firstOrCreate(['name' => 'guru']);
        $roleGuru->syncPermissions([
            'view-jadwal-mapel',
            'input-absensi-mapel',
            'view-absensi-mapel',
        ]);

        // Wali Kelas
        $roleWaliKelas = Role::firstOrCreate(['name' => 'walikelas']);
        $roleWaliKelas->syncPermissions([
            'view-kelas-binaan',
            'input-absensi-harian',
            'view-absensi-harian',
            'cetak-rekap-absensi',
        ]);

        // Guru BK
        $roleBK = Role::firstOrCreate(['name' => 'guru_bk']);
        $roleBK->syncPermissions([
            'manage-catatan-pelanggaran',
        ]);

        // Siswa
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa']);
        $roleSiswa->syncPermissions([
            'view-portal-siswa',
            'view-rekap-presensi-pribadi',
        ]);
    }
}