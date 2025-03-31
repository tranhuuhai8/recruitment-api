<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dataInsert = [
            [
                'name' => 'Admin',
                'mail_address' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Employer',
                'mail_address' => 'employer@gmail.com',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_EMPLOYER,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Candidate',
                'mail_address' => 'candidate@gmail.com',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_CANDIDATE,
                'status' => User::STATUS_ACTIVE,
            ],
        ];

        User::truncate();
        User::insert($dataInsert);
    }
}
