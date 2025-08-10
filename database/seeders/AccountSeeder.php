<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataInsert = [
            [
                'mail_address' => 'admin@gmail.com',
                'password' => Hash::make('123456Hh@'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'mail_address' => 'company@gmail.com',
                'password' => Hash::make('123456Hh@'),
                'role' => User::ROLE_COMPANY,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'mail_address' => 'applicant@gmail.com',
                'password' => Hash::make('123456Hh@'),
                'role' => User::ROLE_APPLICANT,
                'status' => User::STATUS_ACTIVE,
            ],
        ];

        User::truncate();
        User::insert($dataInsert);
    }
}
