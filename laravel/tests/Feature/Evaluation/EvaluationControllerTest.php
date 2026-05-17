<?php

namespace Tests\Feature\Evaluation;

use App\Enums\RightEnum;
use App\Models\User;
use Database\Seeders\RightSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserWithRight([
            RightEnum::EvaluationIndex,
            RightEnum::EvaluationSearch,
            RightEnum::EvaluationCreate,
            RightEnum::EvaluationView,
            RightEnum::EvaluationViewAll,
            RightEnum::EvaluationEdit,
            RightEnum::EvaluationEditAll,
            RightEnum::EvaluationDestroy,
            RightEnum::EvaluationDestroyAll
        ]);
    }
    public function test_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('evaluation.index'));

        $response->assertOk();
        $this->assertRights(RightEnum::EvaluationIndex, 'evaluation.index');
    }
}
