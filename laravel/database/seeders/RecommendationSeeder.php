<?php

namespace Database\Seeders;

use App\Enums\RecommendationEnum;
use App\Enums\RightEnum;
use App\Models\Recommendation;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    public function run(): void
    {
        $recommendations = [
            $this->createRecommendation(RecommendationEnum::firstTeamImmediately, 'Sofort Leistungsteam'),
            $this->createRecommendation(RecommendationEnum::firstTeamProspect, 'Perspektive Leistungsteam'),
            $this->createRecommendation(RecommendationEnum::grassrootsFootball, 'Breitenfußball'),
            $this->createRecommendation(RecommendationEnum::continueMonitoring, 'Weiter Beobachten'),
        ];

        foreach ($recommendations as $recommendation) {
            Recommendation::updateOrCreate($recommendation);
        }
    }

    private function createRecommendation(RecommendationEnum $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
        ];
    }
}
