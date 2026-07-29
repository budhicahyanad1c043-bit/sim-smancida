<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Inisialisasi Role
        $roles = ['admin', 'guru', 'walikelas', 'guru_bk', 'kepala_sekolah', 'siswa'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $defaultPassword = Hash::make('password123');

        // 2. Admin OpsKSP
        $admin = User::updateOrCreate(
            ['email' => 'admin@opsksp.online'],
            ['name' => 'Administrator OpsKSP', 'password' => $defaultPassword]
        );
        $admin->syncRoles(['admin']);

        // 3. Guru
        $userGuru = User::updateOrCreate(
            ['email' => 'guru@opsksp.online'],
            ['name' => 'Budi Santoso, S.Pd.', 'password' => $defaultPassword]
        );
        $userGuru->syncRoles(['guru']);

        Guru::updateOrCreate(
            ['user_id' => $userGuru->id],
            ['nama_guru' => 'Budi Santoso, S.Pd.', 'nip' => '198507152010011001', 'gender' => 'L']
        );

        // 4. Wali Kelas + Kelas Dummy
        $userWali = User::updateOrCreate(
            ['email' => 'walikelas@opsksp.online'],
            ['name' => 'Siti Aminah, M.Pd.', 'password' => $defaultPassword]
        );
        $userWali->syncRoles(['walikelas']);

        $waliGuru = Guru::updateOrCreate(
            ['user_id' => $userWali->id],
            ['nama_guru' => 'Siti Aminah, M.Pd.', 'nip' => '198803222012022002', 'gender' => 'P']
        );

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'X IPA 1'],
            ['tahun_ajaran' => '2026/2027', 'walikelas_id' => $waliGuru->id]
        );

        // 5. Siswa
        $userSiswa = User::updateOrCreate(
            ['email' => 'siswa@opsksp.online'],
            ['name' => 'Rizky Pratama', 'password' => $defaultPassword]
        );
        $userSiswa->syncRoles(['siswa']);

        Siswa::updateOrCreate(
            ['user_id' => $userSiswa->id],
            ['kelas_id' => $kelas->id, 'nama_siswa' => 'Rizky Pratama', 'gender' => 'L', 'nisn' => '0051234567']
        );
    }
}