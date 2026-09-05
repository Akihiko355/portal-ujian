<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@portal-ujian.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        $departments = [
            ['name' => 'Teknik Informatika', 'code' => 'TI', 'description' => 'Departemen Teknik Informatika'],
            ['name' => 'Sistem Informasi', 'code' => 'SI', 'description' => 'Departemen Sistem Informasi'],
            ['name' => 'Manajemen Informatika', 'code' => 'MI', 'description' => 'Departemen Manajemen Informatika'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        $subjects = [
            ['name' => 'Pemrograman Web', 'code' => 'PW001', 'credits' => 3],
            ['name' => 'Basis Data', 'code' => 'BD001', 'credits' => 3],
            ['name' => 'Jaringan Komputer', 'code' => 'JK001', 'credits' => 3],
            ['name' => 'Algoritma dan Struktur Data', 'code' => 'ASD001', 'credits' => 4],
            ['name' => 'Sistem Operasi', 'code' => 'SO001', 'credits' => 3],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }
    }
}