<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use File;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::truncate();
        JobCategory::truncate();

        $dataCity = [];
        $jsonCity = File::get("database/data/city.json");
        $dataJsonCity = json_decode($jsonCity);
        foreach ($dataJsonCity as $item) {
            $dataCity[] = [
                'name' => $item->name,
                'description' => $item->code,
                'status' => City::STATUS_SHOW,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $dataJobCategory = [];
        $jsonJobCategory = File::get("database/data/jobCategory.json");
        $dataJsonJobCategory = json_decode($jsonJobCategory);
        foreach ($dataJsonJobCategory as $item) {
            $dataJobCategory[] = [
                'name' => $item->name,
                'description' => '',
                'status' => JobCategory::STATUS_SHOW,
                'type' => JobCategory::TYPE_DEFAULT,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        JobCategory::insert($dataJobCategory);
        City::insert($dataCity);
    }
}
