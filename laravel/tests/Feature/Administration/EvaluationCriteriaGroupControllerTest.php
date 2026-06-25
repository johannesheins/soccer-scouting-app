<?php

namespace Tests\Feature\Administration;

use App\Models\EvaluationCriteriaGroup;

class EvaluationCriteriaGroupControllerTest extends AdministrationTestCase
{
    public function test_index()
    {
        EvaluationCriteriaGroup::factory(3)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria-group.index'));

        $this->assertAdministrationRoute('evaluation-criteria-group.index', 'administration/evaluation-criteria-group/evaluation-criteria-group-index');
        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('administration/evaluation-criteria-group/evaluation-criteria-group-index')
                ->has('evaluation_criteria_groups', 3)
        );
    }

    public function test_create()
    {
        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria-group.create'));

        $this->assertAdministrationRoute('evaluation-criteria-group.create', 'administration/evaluation-criteria-group/evaluation-criteria-group-create');
        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('administration/evaluation-criteria-group/evaluation-criteria-group-create')
        );
    }

    public function test_store()
    {
        $data = EvaluationCriteriaGroup::factory()->make();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('evaluation-criteria-group.store'), [
                'name' => $data->name,
            ]);

        $this->assertDatabaseHas('evaluation_criteria_groups', ['name' => $data->name]);
        $response->assertRedirect(route('evaluation-criteria-group.index'));
    }

    public function test_store_empty_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('evaluation-criteria-group.store'), ['name' => '']);

        $response->assertInvalid(['name']);
    }

    public function test_show()
    {
        $group = EvaluationCriteriaGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria-group.show', $group->id));

        $response->assertNotFound();
    }

    public function test_edit()
    {
        $group = EvaluationCriteriaGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('evaluation-criteria-group.edit', $group->id));

        $this->assertAdministrationRoute(['evaluation-criteria-group.edit', $group->id], 'administration/evaluation-criteria-group/evaluation-criteria-group-edit');
        $response->assertOk();
        $response->assertInertia(
            fn($page) => $page
                ->component('administration/evaluation-criteria-group/evaluation-criteria-group-edit')
                ->has('evaluationCriteriaGroup')
        );
    }

    public function test_update()
    {
        $group = EvaluationCriteriaGroup::factory()->create();
        $newName = 'Updated Group';

        $response = $this->actingAs($this->administratorUser)
            ->put(route('evaluation-criteria-group.update', $group->id), [
                'name' => $newName,
            ]);

        $this->assertDatabaseHas('evaluation_criteria_groups', ['id' => $group->id, 'name' => $newName]);
        $response->assertRedirect(route('evaluation-criteria-group.index'));
    }

    public function test_update_empty_request()
    {
        $group = EvaluationCriteriaGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('evaluation-criteria-group.update', $group->id), ['name' => '']);

        $response->assertInvalid(['name']);
    }

    public function test_destroy()
    {
        $group = EvaluationCriteriaGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->delete(route('evaluation-criteria-group.destroy', $group->id));

        $this->assertDatabaseMissing('evaluation_criteria_groups', ['id' => $group->id]);
        $response->assertRedirect(route('evaluation-criteria-group.index'));
    }
}
