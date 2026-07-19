<?php

namespace Tests\Feature\Evaluation;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\RightEnum;
use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use App\Models\User;
use App\Services\EvaluationSearchService;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private EvaluationSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EvaluationSearchService();
        $this->user = $this->createUserWithRight([
            RightEnum::EvaluationSearch,
            RightEnum::EvaluationViewAll,
        ]);
    }

    #region criteria_scores_from
    public function test_filters_by_criteria_score_from(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $match = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $match->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 8,
        ]);

        $noMatch = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $noMatch->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 2,
        ]);

        $result = $this->search(['criteria_scores_from' => [$criteria->id => 5]], $this->user);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }

    public function test_no_criteria_scores_from_filter_returns_all(): void
    {
        Evaluation::factory()->count(3)->create();

        $result = $this->search([], $this->user);
        $this->assertCount(3, $result);
    }

    public function test_empty_criteria_scores_from_filter_returns_all(): void
    {
        Evaluation::factory()->count(3)->create();

        $result = $this->search(['criteria_scores_from' => [], $this->user]);
        $this->assertCount(3, $result);
    }
    #endregion

    #region criteria_scores_to
    public function test_filters_by_criteria_score_to(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $match = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $match->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 2,
        ]);

        $noMatch = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $noMatch->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 8,
        ]);

        $result = $this->search(['criteria_scores_to' => [$criteria->id => 5]], $this->user);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }

    public function test_no_criteria_scores_to_filter_returns_all(): void
    {
        Evaluation::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_criteria_scores_to_filter_returns_all(): void
    {
        Evaluation::factory()->count(3)->create();

        $result = $this->search(['criteria_scores_to' => []], $this->user);
        $this->assertCount(3, $result);
    }
    #endregion

    #region kombinierter Bereich (from + to auf dasselbe Kriterium)
    public function test_filters_by_criteria_score_range(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $tooLow = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $tooLow->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 2,
        ]);

        $match = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $match->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 5,
        ]);

        $tooHigh = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $tooHigh->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 8,
        ]);

        $result = $this->search([
            'criteria_scores_from' => [$criteria->id => 4],
            'criteria_scores_to' => [$criteria->id => 6],
        ], $this->user);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }
    #endregion

    #region mehrere Kriterien (AND über unterschiedliche Kriterien)
    public function test_requires_all_criteria_conditions_to_match(): void
    {
        $criteria1 = EvaluationCriteria::factory()->create();
        $criteria3 = EvaluationCriteria::factory()->create();

        $match = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $match->id,
            'evaluation_criteria_id' => $criteria1->id,
            'score' => 1,
        ]);
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $match->id,
            'evaluation_criteria_id' => $criteria3->id,
            'score' => 8,
        ]);

        $onlyFirstCriteriaMatches = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $onlyFirstCriteriaMatches->id,
            'evaluation_criteria_id' => $criteria1->id,
            'score' => 1,
        ]);
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $onlyFirstCriteriaMatches->id,
            'evaluation_criteria_id' => $criteria3->id,
            'score' => 2,
        ]);

        // (score >= 1 AND criteria_id = criteria1) AND (score >= 8 AND criteria_id = criteria3)
        $result = $this->search([
            'criteria_scores_from' => [
                $criteria1->id => 1,
                $criteria3->id => 8,
            ],
        ], $this->user);

        $this->assertCount(1, $result);
        $this->assertSame($match->id, $result->first()->id);
    }
    #endregion

    #region leere Ergebnisse
    public function test_returns_empty_collection_when_no_match(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $evaluation = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
            'score' => 2,
        ]);

        $result = $this->search(['criteria_scores_from' => [$criteria->id => 8]], $this->user);

        $this->assertCount(0, $result);
    }
    #endregion

    #region with (Eager Loading)
    public function test_eager_loads_given_relations(): void
    {
        $criteria = EvaluationCriteria::factory()->create();
        $evaluation = Evaluation::factory()->create();
        EvaluationCriteriaScore::factory()->create([
            'evaluation_id' => $evaluation->id,
            'evaluation_criteria_id' => $criteria->id,
        ]);

        $result = $this->service->searchEvaluations(new EvaluationSearchDTO([]), $this->user, ['player', 'homeTeam', 'awayTeam', 'criteriaScores']);

        $this->assertTrue($result->first()->relationLoaded('player'));
        $this->assertTrue($result->first()->relationLoaded('homeTeam'));
        $this->assertTrue($result->first()->relationLoaded('awayTeam'));
        $this->assertTrue($result->first()->relationLoaded('criteriaScores'));
    }
    #endregion

    #region EvaluationViewAll right
    public function test_returns_only_own_evaluations_when_user_lacks_view_all_right(): void
    {
        $user = $this->createUserWithRight([RightEnum::EvaluationSearch]);

        $own = Evaluation::factory()->create(['created_by' => $user->id]);
        Evaluation::factory()->create();

        $result = $this->service->searchEvaluations(new EvaluationSearchDTO([]), $user, []);

        $this->assertCount(1, $result);
        $this->assertSame($own->id, $result->first()->id);
    }

    public function test_returns_all_evaluations_when_user_has_view_all_right(): void
    {
        Evaluation::factory()->create(['created_by' => $this->user->id]);
        Evaluation::factory()->count(2)->create();

        $result = $this->service->searchEvaluations(new EvaluationSearchDTO([]), $this->user, []);

        $this->assertCount(3, $result);
    }
    #endregion

    private function search(array $params): Collection
    {
        return $this->service->searchEvaluations(new EvaluationSearchDTO($params), $this->user, []);
    }
}
