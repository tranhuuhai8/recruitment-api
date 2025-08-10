<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use File;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobCategory::truncate();

        $data = [];
        $jsonJobCategory = File::get("database/data/categories.json");
        $dataJsonJobCategory = json_decode($jsonJobCategory);

        foreach ($dataJsonJobCategory as $item) {
            $data[] = [
                'name' => $item->name,
                'description' => $item->description,
                'status' => JobCategory::STATUS_SHOW,
                'type' => JobCategory::TYPE_DEFAULT,
                'parent_id' => $item->parent_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        JobCategory::insert($data);
    }
}
