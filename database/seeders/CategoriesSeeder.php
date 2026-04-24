<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $services = [
            ['name' => 'Plumbing', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Electrical', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Painting', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Carpentry', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'AC Repair', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Appliance', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('categories')->upsert($services, ['name'], ['updated_at']);
    }
}
