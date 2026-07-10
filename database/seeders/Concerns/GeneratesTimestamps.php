<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Carbon;

trait GeneratesTimestamps
{
    /**
     * Build $count strictly increasing [created_at, updated_at] pairs spanning
     * from $startsAt up to now, with a random gap (in minutes) between rows.
     *
     * @return array<int, array{created_at: Carbon, updated_at: Carbon}>
     */
    protected function sequentialTimestamps(
        int $count,
        string $startsAt = '2026-05-01',
        int $minGapMinutes = 30,
        int $maxGapMinutes = 2880
    ): array {
        if ($count <= 0) {
            return [];
        }

        $now = Carbon::now();
        $start = Carbon::parse($startsAt);
        $availableMinutes = max($start->diffInMinutes($now), $count);

        $gaps = [];
        for ($i = 0; $i < $count; $i++) {
            $gaps[] = fake()->numberBetween($minGapMinutes, $maxGapMinutes);
        }

        // Normalise the gaps (up or down) so the sequence always stretches
        // across the full window instead of only using part of it - leaving
        // a little headroom so the last record isn't stamped exactly "now".
        $budget = (int) ($availableMinutes * 0.95);
        $totalGap = array_sum($gaps);

        if ($totalGap > 0 && $budget > 0) {
            $scale = $budget / $totalGap;
            $gaps = array_map(fn ($gap) => max(1, (int) round($gap * $scale)), $gaps);
        }

        $cursor = $start->copy();
        $result = [];

        foreach ($gaps as $gap) {
            $cursor = $cursor->copy()->addMinutes($gap);
            $createdAt = $cursor->copy();

            $result[] = [
                'created_at' => $createdAt,
                'updated_at' => $this->maybeUpdatedAt($createdAt),
            ];
        }

        return $result;
    }

    /**
     * Most rows keep updated_at == created_at; a minority get "edited later".
     */
    protected function maybeUpdatedAt(Carbon $createdAt, int $chancePercent = 30, int $maxDays = 60): Carbon
    {
        if (!fake()->boolean($chancePercent)) {
            return $createdAt->copy();
        }

        $updatedAt = $createdAt->copy()->addDays(fake()->numberBetween(1, $maxDays));
        $now = Carbon::now();

        return $updatedAt->greaterThan($now) ? $now->copy() : $updatedAt;
    }

    /**
     * Push $base forward by a random delay, guaranteed to land strictly after
     * $floor (used to keep a derived table's created_at increasing with id
     * even though each row's delay is randomised independently).
     */
    protected function afterWithFloor(Carbon $base, int $minMinutes, int $maxMinutes, ?Carbon $floor): Carbon
    {
        $candidate = $base->copy()->addMinutes(fake()->numberBetween($minMinutes, $maxMinutes));

        if ($floor && $candidate->lessThanOrEqualTo($floor)) {
            $candidate = $floor->copy()->addMinutes(fake()->numberBetween(1, 30));
        }

        $now = Carbon::now();

        return $candidate->greaterThan($now) ? $now->copy() : $candidate;
    }
}
