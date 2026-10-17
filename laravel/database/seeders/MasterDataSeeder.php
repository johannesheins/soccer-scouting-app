<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed the database with masterdata
     * Is automatically called
     */
    public function run(): void
    {
        $this->call(PositionSeeder::class);

        $this->call(RecommendationSeeder::class);
    }
}
