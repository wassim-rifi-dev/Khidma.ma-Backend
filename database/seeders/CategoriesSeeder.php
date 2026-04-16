<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                "name" => "Plumbing",
            ],
            [
                "name" => "Electrical",
            ],
            [
                "name" => "Painting",
            ],
            [
                "name" => "Carpentry",
            ],
            [
                "name" => "AC Repair",
            ],
            [
                "name" => "Appliance",
            ],
        ];

        DB::table('categories')->insert($services);
    }
}
