<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::truncate();

        $data = $ids = [];
        $defaultData = [
            'description' => '',
            'status' => City::STATUS_SHOW,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $jsonCity = File::get("database/data/cities.json");
        $dataJsonCity = json_decode($jsonCity);

        $indexCity = 1;
        foreach ($dataJsonCity as $item) {
            $data[] = array_merge($defaultData, [
                'name' => $item->name,
                'parent_id' => null,
            ]);

            $ids[$item->name] = $indexCity;
            $indexCity++;
        }

        foreach ($dataJsonCity as $item) {
            foreach ($item->data as $childName) {
                $data[] = array_merge($defaultData, [
                    'name' => $childName,
                    'parent_id' => $ids[$item->name] ?? null,
                ]);
            }
        }
        City::insert($data);
    }
}
