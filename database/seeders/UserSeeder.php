<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Illuminate\Database\Seeder;

/**
 * Builds the pool of "registered" users that CompanySeeder / ApplicantSeeder
 * will later pick a subset of to attach a full profile to. The remainder
 * stay as accounts that registered but never completed their profile.
 *
 * The applicant pool is intentionally larger than the company pool so the
 * final applicants table outnumbers the companies table.
 */
class UserSeeder extends Seeder
{
    use GeneratesTimestamps;

    private const WINDOW_START = '2026-05-01';

    public function run(): void
    {
        $this->createPool(User::ROLE_COMPANY, activeCount: 100, unverifiedCount: 20, lockedCount: 9);
        $this->createPool(User::ROLE_APPLICANT, activeCount: 150, unverifiedCount: 25, lockedCount: 10);
    }

    private function createPool(int $role, int $activeCount, int $unverifiedCount, int $lockedCount): void
    {
        $poolSize = $activeCount + $unverifiedCount + $lockedCount + 1; // +1 for the demo account
        $remaining = $poolSize - User::where('role', $role)->count();
        if ($remaining <= 0) {
            return;
        }

        $statuses = array_merge(
            array_fill(0, $activeCount, User::STATUS_ACTIVE),
            array_fill(0, $unverifiedCount, User::STATUS_UNVERIFIED),
            array_fill(0, $lockedCount, User::STATUS_LOCKED),
        );
        shuffle($statuses);
        $statuses = array_slice($statuses, 0, $remaining);

        $timestamps = $this->sequentialTimestamps($remaining, self::WINDOW_START, 10, 500);

        foreach ($statuses as $index => $status) {
            $factory = $role === User::ROLE_COMPANY ? User::factory()->company() : User::factory()->applicant();
            $createdAt = $timestamps[$index]['created_at'];

            $factory->create([
                'status' => $status,
                'email_verified_at' => $status === User::STATUS_UNVERIFIED ? null : $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $timestamps[$index]['updated_at'],
            ]);
        }
    }
}
