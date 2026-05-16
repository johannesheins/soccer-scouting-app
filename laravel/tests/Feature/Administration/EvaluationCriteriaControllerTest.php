<?php

namespace Tests\Feature\Administration;

use App\Models\EvaluationCriteria;

class EvaluationCriteriaControllerTest extends AdministrationTestCase
{
    public function test_index()
    {
        EvaluationCriteria::factory(5)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria.index'));

        $this->assertAdministrationRoute('evaluation-criteria.index', 'administration/evaluation-criteria/evaluation-criteria-index');
        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('administration/evaluation-criteria/evaluation-criteria-index')
                ->has('evaluation_criteria', 5)
        );
    }

    public function test_store()
    {
        $data = EvaluationCriteria::factory()->make();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('evaluation-criteria.store'), [
                'name' => $data->name,
                'minimum_player_age' => $data->minimum_player_age,
                'multiplier' => $data->multiplier,
            ]);

        $this->assertAdministrationRoute('evaluation-criteria.index', 'administration/evaluation-criteria/evaluation-criteria-index');
        $this->assertDatabaseHas('evaluation_criteria', ['name' => $data->name]);
        $response->assertCreated();
    }

    public function test_store_empty_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('evaluation-criteria.store'), [
                'name' => '',
                'minimum_player_age' => '',
            ]);

        $response->assertInvalid(['name', 'minimum_player_age']);
    }

    public function test_show()
    {
        $criterion = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria.show', $criterion->id));

        $this->assertAdministrationRoute(['evaluation-criteria.show', $criterion->id]);
        $response->assertOk();
        $response->assertJson(['id' => $criterion->id]);
    }

    public function test_update()
    {
        $criterion = EvaluationCriteria::factory()->create();
        $newName = 'Updated Criterion';

        $response = $this->actingAs($this->administratorUser)
            ->put(route('evaluation-criteria.update', $criterion->id), [
                'name' => $newName,
                'minimum_player_age' => $criterion->minimum_player_age,
                'multiplier' => $criterion->multiplier,
            ]);

        $this->assertAdministrationRoute(['evaluation-criteria.show', $criterion->id]);
        $this->assertDatabaseHas('evaluation_criteria', ['id' => $criterion->id, 'name' => $newName]);
        $response->assertOk();
    }

    public function test_update_empty_request()
    {
        $criterion = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('evaluation-criteria.update', $criterion->id), [
                'name' => '',
                'minimum_player_age' => '',
            ]);

        $response->assertInvalid(['name', 'minimum_player_age']);
    }

    public function test_destroy()
    {
        $criterion = EvaluationCriteria::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->delete(route('evaluation-criteria.destroy', $criterion->id));

        $this->assertAdministrationRoute(['evaluation-criteria.destroy', $criterion->id]);
        $this->assertDatabaseMissing('evaluation_criteria', ['id' => $criterion->id]);
        $response->assertOk();
    }
}