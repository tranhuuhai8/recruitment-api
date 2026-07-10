<?php

namespace App\Services\Concerns;

use Carbon\Carbon;

trait ResolvesDashboardDateRange
{
    /**
     * resolveDateRange
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return array
     */
    private function resolveDateRange(?string $range, ?string $from, ?string $to): array
    {
        $today = Carbon::today();
        $startOfThisMonth = $today->copy()->startOfMonth();

        return match ($range) {
            '7d' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            'last_month' => [
                $startOfThisMonth->copy()->subMonth(),
                $startOfThisMonth->copy()->subDay()->endOfDay(),
            ],
            'custom' => $from && $to
                ? [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]
                : [$startOfThisMonth, $today->copy()->endOfMonth()],
            default => [$startOfThisMonth, $today->copy()->endOfMonth()],
        };
    }
}
