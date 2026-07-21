<?php

namespace Tests\Unit\Evaluation;

use App\DTOs\EvaluationSearchDTO;
use PHPUnit\Framework\TestCase;

class EvaluationSearchDTOTest extends TestCase
{
    // player_ids
    public function test_player_ids_is_mapped(): void
    {
        $dto = new EvaluationSearchDTO(['player_ids' => [1, 3]]);
        $this->assertSame([1, 3], $dto->playerIds);
    }

    public function test_player_ids_defaults_to_empty_array_when_missing(): void
    {
        $dto = new EvaluationSearchDTO([]);
        $this->assertSame([], $dto->playerIds);
    }

    public function test_player_ids_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new EvaluationSearchDTO(['player_ids' => null]);
        $this->assertSame([], $dto->playerIds);
    }

    // criteria_scores_from
    public function test_criteria_scores_from_is_mapped(): void
    {
        $dto = new EvaluationSearchDTO(['criteria_scores_from' => [1 => 3, 3 => 8]]);
        $this->assertSame([1 => 3, 3 => 8], $dto->criteria_scores_from);
    }

    public function test_criteria_scores_from_defaults_to_empty_array_when_missing(): void
    {
        $dto = new EvaluationSearchDTO([]);
        $this->assertSame([], $dto->criteria_scores_from);
    }

    public function test_criteria_scores_from_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new EvaluationSearchDTO(['criteria_scores_from' => null]);
        $this->assertSame([], $dto->criteria_scores_from);
    }

    // criteria_scores_to
    public function test_criteria_scores_to_is_mapped(): void
    {
        $dto = new EvaluationSearchDTO(['criteria_scores_to' => [1 => 3, 3 => 8]]);
        $this->assertSame([1 => 3, 3 => 8], $dto->criteria_scores_to);
    }

    public function test_criteria_scores_to_defaults_to_empty_array_when_missing(): void
    {
        $dto = new EvaluationSearchDTO([]);
        $this->assertSame([], $dto->criteria_scores_to);
    }

    public function test_criteria_scores_to_defaults_to_empty_array_when_explicitly_null(): void
    {
        $dto = new EvaluationSearchDTO(['criteria_scores_to' => null]);
        $this->assertSame([], $dto->criteria_scores_to);
    }

    // vollständiger Input
    public function test_all_fields_are_mapped_correctly(): void
    {
        $dto = new EvaluationSearchDTO([
            'player_ids' => [1, 3],
            'criteria_scores_from' => [1 => 1, 3 => 8],
            'criteria_scores_to' => [1 => 10, 3 => 10],
        ]);

        $this->assertSame([1, 3], $dto->playerIds);
        $this->assertSame([1 => 1, 3 => 8], $dto->criteria_scores_from);
        $this->assertSame([1 => 10, 3 => 10], $dto->criteria_scores_to);
    }
}
