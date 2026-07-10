<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\User;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ApplicantSeeder extends Seeder
{
    use GeneratesTimestamps;

    // Kept higher than CompanySeeder::TARGET so applicants outnumber companies.
    private const TARGET = 150;

    private const WINDOW_START = '2026-05-01';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $remaining = self::TARGET - Applicant::count();
        if ($remaining <= 0) {
            return;
        }

        $demoUser = User::where('mail_address', 'applicant@gmail.com')->first();
        $users = collect();

        if ($demoUser && !Applicant::where('user_id', $demoUser->id)->exists()) {
            $users->push($demoUser);
            $remaining--;
        }

        $candidates = User::where('role', User::ROLE_APPLICANT)
            ->where('status', User::STATUS_ACTIVE)
            ->whereDoesntHave('applicant')
            ->when($demoUser, fn ($query) => $query->where('id', '!=', $demoUser->id))
            ->inRandomOrder()
            ->limit($remaining)
            ->get();

        $users = $users->merge($candidates)->sortBy(
            fn (User $user) => $user->created_at ?? Carbon::parse(self::WINDOW_START)
        )->values();

        $floor = null;
        foreach ($users as $user) {
            $baseline = $user->created_at ?? Carbon::parse(self::WINDOW_START);
            $createdAt = $this->afterWithFloor($baseline, 10, 4320, $floor);
            $floor = $createdAt;

            Applicant::factory()->create([
                'user_id' => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $this->maybeUpdatedAt($createdAt, 20, 90),
            ]);
        }
    }
}
