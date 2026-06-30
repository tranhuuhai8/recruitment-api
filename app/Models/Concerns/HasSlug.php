<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait: generate the slug right after insert (once the
     * primary key is known) and regenerate it whenever the source
     * column changes on an existing record.
     */
    public static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            if ($model->exists && $model->isDirty($model->slugSourceColumn())) {
                $model->slug = $model->generateUniqueSlug();
            }
        });

        static::created(function ($model) {
            $model->refreshSlug();
        });
    }

    /**
     * The column the slug is derived from (e.g. "title", "name").
     */
    protected function slugSourceColumn(): string
    {
        return 'title';
    }

    /**
     * Recompute and persist the slug for this model without re-firing
     * the slug-generation hooks. Used by the initial create() flow and
     * by backfill seeders.
     */
    public function refreshSlug(): static
    {
        $this->slug = $this->generateUniqueSlug();
        $this->saveQuietly();

        return $this;
    }

    /**
     * Build a slug from the source column, appending the model's id
     * when the base slug is already taken by another row (including
     * soft-deleted ones, since the column stays unique at the DB level).
     */
    protected function generateUniqueSlug(): string
    {
        $base = Str::slug((string) $this->{$this->slugSourceColumn()});

        $isTaken = static::withTrashed()
            ->where('slug', $base)
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->exists();

        return $isTaken ? $base . '-' . $this->getKey() : $base;
    }
}
