<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\RightGroup;
use App\Models\User;
use App\Models\UserGroup;
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

        $this->call(RightSeeder::class);

        $this->call(RecommendationSeeder::class);
    }
}
