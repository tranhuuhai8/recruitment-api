<?php

namespace Database\Seeders\Concerns;

use App\Models\User;

/**
 * Looks up the fixed demo accounts (seeded by AccountSeeder) so other
 * seeders can deliberately load them up with extra data for manual testing.
 */
trait ResolvesDemoAccounts
{
    protected function demoCompanyId(): ?int
    {
        return User::where('mail_address', 'company@gmail.com')->first()?->company?->id;
    }

    protected function demoApplicantId(): ?int
    {
        return User::where('mail_address', 'applicant@gmail.com')->first()?->applicant?->id;
    }
}
