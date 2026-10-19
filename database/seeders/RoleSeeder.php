<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
            'is_attendance_required' => false,
        ]);

        Role::create([
            'name' => 'ceo',
            'display_name' => 'Chief Executive Officer',
            'is_attendance_required' => false,
        ]);

        Role::create([
            'name' => 'finance',
            'display_name' => 'Finance & HRD',
            'is_attendance_required' => true,
        ]);

        Role::create([
            'name' => 'karyawan',
            'display_name' => 'Karyawan Regular',
            'is_attendance_required' => true,
        ]);

        Role::create([
            'name' => 'intern',
            'display_name' => 'Magang / PKL',
            'is_attendance_required' => true,
        ]);
    }
}
