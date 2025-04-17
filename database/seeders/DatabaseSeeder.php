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
                'mail_address' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'mail_address' => 'employer@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => User::ROLE_COMPANY,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'mail_address' => 'applicant@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => User::ROLE_APPLICANT,
                'status' => User::STATUS_ACTIVE,
            ],
        ];

        User::truncate();
        User::insert($dataInsert);
    }
}
