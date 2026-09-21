<?php

namespace Tests\Feature\Evaluation;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\EvaluationTypes;
use App\Enums\Permission\EvaluationPermissions;
use App\Enums\Permission\GameEvaluationPermissions;
use App\Enums\Permission\PlayerEvaluationPermissions;
use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use App\Models\GameEvaluation;
use App\Models\Player;
use App\Models\PlayerEvaluation;
use App\Models\User;
use App\Services\EvaluationSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
            EvaluationPermissions::Search,
            GameEvaluationPermissions::ViewAll,
            PlayerEvaluationPermissions::ViewAll,
        ]);
    }

    #region criteria_scores_from
    public function test_filters_by_criteria_score_from(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $match = GameEvaluation::factory()->create();
        $this->addScore($match, $criteria, 8);

        $noMatch = GameEvaluation::factory()->create();
        $this->addScore($noMatch, $criteria, 2);

        $result = $this->search(['criteria_scores_from' => [$criteria->id => 5]]);

        $this->assertSame([$this->key($match)], $this->keys($result));
    }

    public function test_no_criteria_scores_from_filter_returns_all(): void
    {
        GameEvaluation::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_criteria_scores_from_filter_returns_all(): void
    {
        GameEvaluation::factory()->count(3)->create();

        $result = $this->search(['criteria_scores_from' => []]);
        $this->assertCount(3, $result);
    }
    #endregion

    #region criteria_scores_to
    public function test_filters_by_criteria_score_to(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $match = GameEvaluation::factory()->create();
        $this->addScore($match, $criteria, 2);

        $noMatch = GameEvaluation::factory()->create();
        $this->addScore($noMatch, $criteria, 8);

        $result = $this->search(['criteria_scores_to' => [$criteria->id => 5]]);

        $this->assertSame([$this->key($match)], $this->keys($result));
    }

    public function test_no_criteria_scores_to_filter_returns_all(): void
    {
        GameEvaluation::factory()->count(3)->create();

        $result = $this->search([]);
        $this->assertCount(3, $result);
    }

    public function test_empty_criteria_scores_to_filter_returns_all(): void
    {
        GameEvaluation::factory()->count(3)->create();

        $result = $this->search(['criteria_scores_to' => []]);
        $this->assertCount(3, $result);
    }
    #endregion

    #region kombinierter Bereich (from + to auf dasselbe Kriterium)
    public function test_filters_by_criteria_score_range(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $tooLow = GameEvaluation::factory()->create();
        $this->addScore($tooLow, $criteria, 2);

        $match = GameEvaluation::factory()->create();
        $this->addScore($match, $criteria, 5);

        $tooHigh = GameEvaluation::factory()->create();
        $this->addScore($tooHigh, $criteria, 8);

        $result = $this->search([
            'criteria_scores_from' => [$criteria->id => 4],
            'criteria_scores_to' => [$criteria->id => 6],
        ]);

        $this->assertSame([$this->key($match)], $this->keys($result));
    }
    #endregion

    #region mehrere Kriterien (AND über unterschiedliche Kriterien)
    public function test_requires_all_criteria_conditions_to_match(): void
    {
        $criteria1 = EvaluationCriteria::factory()->create();
        $criteria3 = EvaluationCriteria::factory()->create();

        $match = GameEvaluation::factory()->create();
        $this->addScore($match, $criteria1, 1);
        $this->addScore($match, $criteria3, 8);

        $onlyFirstCriteriaMatches = GameEvaluation::factory()->create();
        $this->addScore($onlyFirstCriteriaMatches, $criteria1, 1);
        $this->addScore($onlyFirstCriteriaMatches, $criteria3, 2);

        // (score >= 1 AND criteria_id = criteria1) AND (score >= 8 AND criteria_id = criteria3)
        $result = $this->search([
            'criteria_scores_from' => [
                $criteria1->id => 1,
                $criteria3->id => 8,
            ],
        ]);

        $this->assertSame([$this->key($match)], $this->keys($result));
    }
    #endregion

    #region leere Ergebnisse
    public function test_returns_empty_collection_when_no_match(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $this->addScore(GameEvaluation::factory()->create(), $criteria, 2);
        $this->addScore(PlayerEvaluation::factory()->create(), $criteria, 2);

        $result = $this->search(['criteria_scores_from' => [$criteria->id => 8]]);

        $this->assertCount(0, $result);
    }

    public function test_returns_empty_collection_when_there_are_no_evaluations(): void
    {
        $this->assertCount(0, $this->search([]));
    }
    #endregion

    #region Evaluationstypen (Game + Player)
    public function test_returns_game_and_player_evaluations(): void
    {
        $game = GameEvaluation::factory()->create();
        $player = PlayerEvaluation::factory()->create();

        $result = $this->search([]);

        $this->assertEqualsCanonicalizing([$this->key($game), $this->key($player)], $this->keys($result));
        $this->assertContainsOnlyInstancesOf(Evaluation::class, $result);
    }

    public function test_result_is_a_flat_list_with_sequential_keys(): void
    {
        GameEvaluation::factory()->count(2)->create();
        PlayerEvaluation::factory()->count(2)->create();

        $result = $this->search([]);

        $this->assertSame([0, 1, 2, 3], $result->keys()->all());
    }

    public function test_sorts_evaluations_of_both_types_by_created_at_descending(): void
    {
        $oldestGame = GameEvaluation::factory()->create(['created_at' => Carbon::parse('2026-01-01')]);
        $newestPlayer = PlayerEvaluation::factory()->create(['created_at' => Carbon::parse('2026-03-01')]);
        $middleGame = GameEvaluation::factory()->create(['created_at' => Carbon::parse('2026-02-01')]);
        $middlePlayer = PlayerEvaluation::factory()->create(['created_at' => Carbon::parse('2026-02-15')]);

        $result = $this->search([]);

        $this->assertSame([
            $this->key($newestPlayer),
            $this->key($middlePlayer),
            $this->key($middleGame),
            $this->key($oldestGame),
        ], $this->keys($result));
    }

    public function test_criteria_filter_applies_to_both_types(): void
    {
        $criteria = EvaluationCriteria::factory()->create();

        $gameMatch = GameEvaluation::factory()->create();
        $this->addScore($gameMatch, $criteria, 8);
        $gameNoMatch = GameEvaluation::factory()->create();
        $this->addScore($gameNoMatch, $criteria, 2);

        $playerMatch = PlayerEvaluation::factory()->create();
        $this->addScore($playerMatch, $criteria, 8);
        $playerNoMatch = PlayerEvaluation::factory()->create();
        $this->addScore($playerNoMatch, $criteria, 2);

        $result = $this->search(['criteria_scores_from' => [$criteria->id => 5]]);

        $this->assertEqualsCanonicalizing(
            [$this->key($gameMatch), $this->key($playerMatch)],
            $this->keys($result),
        );
    }

    public function test_player_filter_applies_to_both_types(): void
    {
        $player = Player::factory()->create();

        $gameMatch = GameEvaluation::factory()->create(['player_id' => $player->id]);
        GameEvaluation::factory()->create();
        $playerMatch = PlayerEvaluation::factory()->create(['player_id' => $player->id]);
        PlayerEvaluation::factory()->create();

        $result = $this->search(['player_ids' => [$player->id]]);

        $this->assertEqualsCanonicalizing(
            [$this->key($gameMatch), $this->key($playerMatch)],
            $this->keys($result),
        );
    }
    #endregion

    #region with / withFor (Eager Loading)
    public function test_eager_loads_relations_given_in_with_for_every_type(): void
    {
        $criteria = EvaluationCriteria::factory()->create();
        $game = GameEvaluation::factory()->create();
        $this->addScore($game, $criteria, 5);
        $player = PlayerEvaluation::factory()->create();
        $this->addScore($player, $criteria, 5);

        $result = $this->search([], with: ['criteriaScores', 'creator']);

        $this->assertCount(2, $result);
        foreach ($result as $evaluation) {
            $this->assertTrue($evaluation->relationLoaded('criteriaScores'), $evaluation::class);
            $this->assertTrue($evaluation->relationLoaded('creator'), $evaluation::class);
        }
    }

    public function test_eager_loads_type_specific_relations_from_with_for(): void
    {
        GameEvaluation::factory()->create();
        PlayerEvaluation::factory()->create();

        $result = $this->search([], withFor: [
            EvaluationTypes::GAME->value => ['player', 'homeTeam', 'guestTeam'],
            EvaluationTypes::PLAYER->value => ['player'],
        ]);

        $game = $result->first(fn ($evaluation) => $evaluation instanceof GameEvaluation);
        $this->assertTrue($game->relationLoaded('player'));
        $this->assertTrue($game->relationLoaded('homeTeam'));
        $this->assertTrue($game->relationLoaded('guestTeam'));

        $player = $result->first(fn ($evaluation) => $evaluation instanceof PlayerEvaluation);
        $this->assertTrue($player->relationLoaded('player'));
    }

    public function test_with_for_relations_are_not_loaded_on_other_types(): void
    {
        GameEvaluation::factory()->create();
        PlayerEvaluation::factory()->create();

        $result = $this->search([], withFor: [
            EvaluationTypes::GAME->value => ['homeTeam'],
            EvaluationTypes::PLAYER->value => [],
        ]);

        $game = $result->first(fn ($evaluation) => $evaluation instanceof GameEvaluation);
        $this->assertTrue($game->relationLoaded('homeTeam'));

        $player = $result->first(fn ($evaluation) => $evaluation instanceof PlayerEvaluation);
        $this->assertFalse($player->relationLoaded('player'));
    }
    #endregion

    #region ViewAll rights
    public function test_returns_only_own_evaluations_when_user_lacks_view_all_rights(): void
    {
        $user = $this->createUserWithRight([EvaluationPermissions::Search]);

        $ownGame = GameEvaluation::factory()->create(['created_by' => $user->id]);
        GameEvaluation::factory()->create();
        $ownPlayer = PlayerEvaluation::factory()->create(['created_by' => $user->id]);
        PlayerEvaluation::factory()->create();

        $result = $this->search([], user: $user);

        $this->assertEqualsCanonicalizing([$this->key($ownGame), $this->key($ownPlayer)], $this->keys($result));
    }

    public function test_returns_all_evaluations_when_user_has_view_all_rights(): void
    {
        GameEvaluation::factory()->create(['created_by' => $this->user->id]);
        GameEvaluation::factory()->count(2)->create();
        PlayerEvaluation::factory()->create(['created_by' => $this->user->id]);
        PlayerEvaluation::factory()->count(2)->create();

        $result = $this->search([]);

        $this->assertCount(6, $result);
    }

    public function test_game_view_all_right_does_not_expose_foreign_player_evaluations(): void
    {
        $user = $this->createUserWithRight([
            EvaluationPermissions::Search,
            GameEvaluationPermissions::ViewAll,
        ]);

        $foreignGame = GameEvaluation::factory()->create();
        GameEvaluation::factory()->create(['created_by' => $user->id]);
        $ownPlayer = PlayerEvaluation::factory()->create(['created_by' => $user->id]);
        PlayerEvaluation::factory()->create();

        $result = $this->search([], user: $user);

        $this->assertCount(3, $result);
        $this->assertContains($this->key($foreignGame), $this->keys($result));
        $this->assertContains($this->key($ownPlayer), $this->keys($result));
        $this->assertCount(1, $result->whereInstanceOf(PlayerEvaluation::class));
    }

    public function test_player_view_all_right_does_not_expose_foreign_game_evaluations(): void
    {
        $user = $this->createUserWithRight([
            EvaluationPermissions::Search,
            PlayerEvaluationPermissions::ViewAll,
        ]);

        $foreignPlayer = PlayerEvaluation::factory()->create();
        PlayerEvaluation::factory()->create(['created_by' => $user->id]);
        $ownGame = GameEvaluation::factory()->create(['created_by' => $user->id]);
        GameEvaluation::factory()->create();

        $result = $this->search([], user: $user);

        $this->assertCount(3, $result);
        $this->assertContains($this->key($foreignPlayer), $this->keys($result));
        $this->assertContains($this->key($ownGame), $this->keys($result));
        $this->assertCount(1, $result->whereInstanceOf(GameEvaluation::class));
    }
    #endregion

    private function search(array $params, ?User $user = null, array $with = [], array $withFor = []): Collection
    {
        return $this->service->searchEvaluations(
            dto: new EvaluationSearchDTO($params),
            user: $user ?? $this->user,
            with: $with,
            withFor: $withFor + [
                EvaluationTypes::GAME->value => [],
                EvaluationTypes::PLAYER->value => [],
            ],
        );
    }

    private function addScore(Evaluation $evaluation, EvaluationCriteria $criteria, int $score): void
    {
        $evaluation->criteriaScores()->create([
            'evaluation_criteria_id' => $criteria->id,
            'score' => $score,
        ]);
    }

    /**
     * Game and player evaluations live in separate tables, so ids alone are ambiguous.
     */
    private function key(Evaluation $evaluation): string
    {
        return $evaluation::class . '#' . $evaluation->id;
    }

    /**
     * @return string[]
     */
    private function keys(Collection $evaluations): array
    {
        return $evaluations->map(fn (Evaluation $evaluation) => $this->key($evaluation))->all();
    }
}
