<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleSuperAdmin = Role::where('name', 'super_admin')->first();
        $roleCEO = Role::where('name', 'ceo')->first();
        $roleFinance = Role::where('name', 'finance')->first();
        $roleKaryawan = Role::where('name', 'karyawan')->first();
        $roleIntern = Role::where('name', 'intern')->first();

        // Password Default Sama Semua untuk Testing: "password"
        $defaultPassword = Hash::make('password');

        // 1. Akun Super Admin
        $admin = User::create([
            'name' => 'Super Admin Dev',
            'email' => 'admin@test.com',
            'password' => $defaultPassword,
            'employment_type' => 'KARTAP',
            'basic_salary' => 10000000,
            'can_approve_leaves' => true,
        ]);
        if ($roleSuperAdmin) {
            $admin->roles()->attach([$roleSuperAdmin->id]);
        }

        // 2. Akun CEO
        $ceo = User::create([
            'name' => 'Bapak CEO',
            'email' => 'ceo@test.com',
            'password' => $defaultPassword,
            'employment_type' => 'KARTAP',
            'basic_salary' => 25000000,
            'can_approve_leaves' => false,
        ]);
        if ($roleCEO) {
            $ceo->roles()->attach([$roleCEO->id]);
        }

        // 3. Akun Finance (Multi-Role: Finance + Karyawan)
        $finance = User::create([
            'name' => 'Siti Finance',
            'email' => 'finance@test.com',
            'password' => $defaultPassword,
            'rfid_uid' => '1234567890',
            'employment_type' => 'KARTAP',
            'basic_salary' => 7000000,
            'can_approve_leaves' => true,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Siti Finance',
        ]);
        $financeRoles = array_filter([$roleFinance?->id, $roleKaryawan?->id]);
        $finance->roles()->attach($financeRoles);

        // 4. Akun Karyawan Senior (Diberi Hak Approval Perizinan)
        $manager = User::create([
            'name' => 'Budi Supervisor',
            'email' => 'manager@test.com',
            'password' => $defaultPassword,
            'rfid_uid' => '0987654321',
            'employment_type' => 'KARTAP',
            'basic_salary' => 8500000,
            'can_approve_leaves' => true, // Diberi akses Web untuk approval
            'bank_name' => 'Mandiri',
            'bank_account_number' => '9876543210',
            'bank_account_holder' => 'Budi Supervisor',
        ]);
        if ($roleKaryawan) {
            $manager->roles()->attach([$roleKaryawan->id]);
        }

        // 5. Akun Karyawan Regular (PKWT)
        $karyawan = User::create([
            'name' => 'Andi Staff',
            'email' => 'karyawan@test.com',
            'password' => $defaultPassword,
            'rfid_uid' => '1122334455',
            'employment_type' => 'PKWT',
            'basic_salary' => 4500000,
            'can_approve_leaves' => false,
            'bank_name' => 'BRI',
            'bank_account_number' => '5544332211',
            'bank_account_holder' => 'Andi Staff',
        ]);
        if ($roleKaryawan) {
            $karyawan->roles()->attach([$roleKaryawan->id]);
        }

        // 6. Akun Anak Magang / Intern
        $intern = User::create([
            'name' => 'Rian Intern',
            'email' => 'intern@test.com',
            'password' => $defaultPassword,
            'employment_type' => 'INTERN',
            'basic_salary' => 0,
            'can_approve_leaves' => false,
        ]);
        if ($roleIntern) {
            $intern->roles()->attach([$roleIntern->id]);
        }

        // 7. Karyawan Tambahan untuk Tampilan Realistis HRMS Workforce
        $engineer = User::create([
            'name' => 'Dewi Anastasia',
            'email' => 'dewi@chrono.id',
            'password' => $defaultPassword,
            'rfid_uid' => '3344556677',
            'employment_type' => 'PKWT',
            'basic_salary' => 8500000,
            'can_approve_leaves' => false,
            'bank_name' => 'BCA',
            'bank_account_number' => '8822001199',
            'bank_account_holder' => 'Dewi Anastasia',
        ]);
        if ($roleKaryawan) {
            $engineer->roles()->attach([$roleKaryawan->id]);
        }

        $designer = User::create([
            'name' => 'Fajar Nugraha',
            'email' => 'fajar@chrono.id',
            'password' => $defaultPassword,
            'rfid_uid' => '4455667788',
            'employment_type' => 'PKWT',
            'basic_salary' => 6500000,
            'can_approve_leaves' => false,
            'bank_name' => 'BSI',
            'bank_account_number' => '7112233445',
            'bank_account_holder' => 'Fajar Nugraha',
        ]);
        if ($roleKaryawan) {
            $designer->roles()->attach([$roleKaryawan->id]);
        }

        $hrTalent = User::create([
            'name' => 'Maya Kartika',
            'email' => 'maya@chrono.id',
            'password' => $defaultPassword,
            'rfid_uid' => '5566778899',
            'employment_type' => 'KARTAP',
            'basic_salary' => 7500000,
            'can_approve_leaves' => true,
            'bank_name' => 'Mandiri',
            'bank_account_number' => '137001928374',
            'bank_account_holder' => 'Maya Kartika',
        ]);
        if ($roleKaryawan) {
            $hrTalent->roles()->attach([$roleKaryawan->id]);
        }

        $internMobile = User::create([
            'name' => 'Dimas Setiawan',
            'email' => 'dimas@chrono.id',
            'password' => $defaultPassword,
            'employment_type' => 'INTERN',
            'basic_salary' => 0,
            'can_approve_leaves' => false,
        ]);
        if ($roleIntern) {
            $internMobile->roles()->attach([$roleIntern->id]);
        }
    }
}
