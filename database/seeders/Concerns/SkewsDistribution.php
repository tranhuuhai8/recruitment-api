<?php

namespace Database\Seeders\Concerns;

/**
 * Builds a weighted pool of ids so random selection clusters on a handful
 * of "hot" ids (and optionally one guaranteed spotlight id) instead of
 * spreading evenly - e.g. a few companies with lots of jobs, most with few.
 */
trait SkewsDistribution
{
    /**
     * @param array<int, int> $ids
     * @return array<int, int>
     */
    protected function weightedPool(
        array $ids,
        ?int $spotlightId = null,
        int $hotCount = 12,
        array $hotWeightRange = [10, 25],
        array $normalWeightRange = [1, 4],
        array $spotlightWeightRange = [25, 40]
    ): array {
        if (empty($ids)) {
            return [];
        }

        $hotIds = collect($ids)->random(min($hotCount, count($ids)))->all();

        if ($spotlightId && !in_array($spotlightId, $hotIds, true)) {
            $hotIds[array_key_last($hotIds)] = $spotlightId;
        }

        $pool = [];
        foreach ($ids as $id) {
            $weight = match (true) {
                $id === $spotlightId => fake()->numberBetween(...$spotlightWeightRange),
                in_array($id, $hotIds, true) => fake()->numberBetween(...$hotWeightRange),
                default => fake()->numberBetween(...$normalWeightRange),
            };
            $pool = array_merge($pool, array_fill(0, $weight, $id));
        }

        return $pool;
    }
}
