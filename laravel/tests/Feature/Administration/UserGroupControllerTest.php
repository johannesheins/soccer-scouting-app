<?php

namespace Tests\Feature\Administration;

use App\Enums\AuditEvent;
use App\Models\Right;
use App\Models\RightGroup;
use App\Models\UserGroup;

class UserGroupControllerTest extends AdministrationTestCase
{
    public function test_index()
    {
        $userGroups = UserGroup::factory(14)->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.index'));

        $this->assertAdministrationRoute('administration.user-group.index', 'administration/user-group/user-group-index');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-index')
            ->has('userGroups', 14)
        );
    }

    public function test_create(){
        $rightGroups = RightGroup::factory(3)->create();
        $rightGroups->each(fn($rightGroup) => Right::factory(3)->for($rightGroup)->create());

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.create'));

        $this->assertAdministrationRoute('administration.user-group.create', 'administration/user-group/user-group-create');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-create')
            ->has('rightGroups', 3)
            ->has('rightGroups.0.rights', 3)
            ->has('rightGroups.1.rights', 3)
            ->has('rightGroups.2.rights', 3)
        );
    }

    public function test_store()
    {
        $userGroup = UserGroup::factory()->make();
        $rights = Right::factory(12)->create();

        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => $userGroup->name,
                'rights' => $rights->pluck('id')->toArray(),
            ]);

        $this->assertAdministrationRoute('administration.user-group.create', 'administration/user-group/user-group-create');
        $this->assertDatabaseHas('user_groups', ['name' => $userGroup->name]);
        $this->assertDatabaseHas('user_group_rights', ['right_id' => $rights->first()->id]);

        $response->assertRedirect(route('administration.user-group.index'));
    }

    public function test_store_writes_created_audit_log()
    {
        $rights = Right::factory(3)->create();

        $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => 'Scouts',
                'rights' => $rights->pluck('id')->toArray(),
            ]);

        $userGroup = UserGroup::where('name', 'Scouts')->sole();
        $log = $userGroup->auditLogs()->sole();
        $this->assertSame(AuditEvent::CREATED, $log->event);
        $this->assertSame(UserGroup::class, $log->auditable_type);
        $this->assertNull($log->old_values);
        $this->assertSame('Scouts', $log->new_values['name']);
        $this->assertSame($rights->pluck('id')->sort()->values()->all(), $log->new_values['rights']);
        $this->assertSame($this->administratorUser->id, $log->user_id);
    }

    public function test_store_empty_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => '',
                'rights' => [],
            ]);

        $response->assertInvalid(['name']);
    }

    public function test_store_invalid_request()
    {
        $response = $this->actingAs($this->administratorUser)
            ->post(route('administration.user-group.store'), [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam architecto atque, aut commodi, cumque distinctio doloribus eum explicabo fugit illo inventore ipsam minima natus nemo nesciunt nulla porro possimus quasi quo quod recusandae repellendus reprehenderit sunt totam velit veritatis vitae. Ad adipisci assumenda at, corporis culpa cumque enim eos iure neque perspiciatis quae, quo reiciendis rem repellendus repudiandae tempore vel voluptate, voluptatibus! Aperiam autem blanditiis cum delectus dolores enim excepturi facere hic, id labore maxime natus nisi nostrum quae quaerat quis quod suscipit vel, velit vitae! Ad architecto asperiores at culpa eos ex illo magnam, nesciunt nihil omnis soluta.',
                'rights' => [1, 3, 4]
            ]);

        $response->assertInvalid(['name', 'rights.0']);
    }

    public function test_show()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.show', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.show', $userGroup->id]);
        $response->assertNotFound();
    }

    public function test_edit(){
        $rightGroups = RightGroup::factory(3)->create();
        $rightGroups->each(fn($rightGroup) => Right::factory(3)->for($rightGroup)->create());

        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach($rightGroups->flatMap->rights->take(10));

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.edit', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.edit', $userGroup->id], 'administration/user-group/user-group-edit');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-edit')

            ->has('rightGroups', 3)
            ->has('rightGroups.0.rights', 3)

            ->has('userGroup')
            ->has('userGroup.rights', 9)
            ->where('userGroup.rights.0.id', $userGroup->rights()->first()->id)
        );
    }

    public function test_update()
    {
        $oldRights = Right::factory(2)->create();
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach($oldRights);

        $newName = 'Updated Group';
        $newRights = Right::factory(3)->create();

        $response = $this->actingAs($this->administratorUser)
            ->from(route('administration.user-group.index'))
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => $newName,
                'rights' => $newRights->pluck('id')->toArray(),
            ]);

        $this->assertAdministrationRoute(['administration.user-group.edit', $userGroup->id], 'administration/user-group/user-group-edit');
        $this->assertDatabaseHas('user_groups', ['id' => $userGroup->id, 'name' => $newName]);
        $this->assertDatabaseHas('user_group_rights', ['user_group_id' => $userGroup->id, 'right_id' => $newRights->first()->id]);
        $this->assertDatabaseMissing('user_group_rights', ['user_group_id' => $userGroup->id, 'right_id' => $oldRights->first()->id]);

        $response->assertRedirect(route('administration.user-group.index'));
    }

    public function test_update_writes_updated_audit_log()
    {
        $oldRight = Right::factory()->create();
        $newRight = Right::factory()->create();
        $userGroup = UserGroup::factory()->create(['name' => 'Old Group']);
        $userGroup->rights()->attach($oldRight);

        $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => 'New Group',
                'rights' => [$newRight->id],
            ]);

        $log = $userGroup->auditLogs()->sole();
        $this->assertSame(AuditEvent::UPDATED, $log->event);
        $this->assertSame('Old Group', $log->old_values['name']);
        $this->assertSame([$oldRight->id], $log->old_values['rights']);
        $this->assertSame('New Group', $log->new_values['name']);
        $this->assertSame([$newRight->id], $log->new_values['rights']);
        $this->assertSame($this->administratorUser->id, $log->user_id);
    }

    public function test_update_empty_request()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => '',
                'rights' => [],
            ]);

        $response->assertInvalid(['name']);
    }

    public function test_update_invalid_request()
    {
        $userGroup = UserGroup::factory()->create();

        $response = $this->actingAs($this->administratorUser)
            ->put(route('administration.user-group.update', $userGroup->id), [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam architecto atque, aut commodi, cumque distinctio doloribus eum explicabo fugit illo inventore ipsam minima natus nemo nesciunt nulla porro possimus quasi quo quod recusandae repellendus reprehenderit sunt totam velit veritatis vitae. Ad adipisci assumenda at, corporis culpa cumque enim eos iure neque perspiciatis quae, quo reiciendis rem repellendus repudiandae tempore vel voluptate, voluptatibus! Aperiam autem blanditiis cum delectus dolores enim excepturi facere hic, id labore maxime natus nisi nostrum quae quaerat quis quod suscipit vel, velit vitae! Ad architecto asperiores at culpa eos ex illo magnam, nesciunt nihil omnis soluta.',
                'rights' => [1, 3, 4]
            ]);

        $response->assertInvalid(['name', 'rights.0']);
    }

    public function test_destroy()
    {
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach(Right::factory(3)->create());

        $response = $this->actingAs($this->administratorUser)
            ->from(route('administration.user-group.index'))
            ->delete(route('administration.user-group.destroy', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.destroy', $userGroup->id]);
        $this->assertDatabaseMissing('user_groups', ['id' => $userGroup->id]);
        $this->assertDatabaseMissing('user_group_rights', ['user_group_id' => $userGroup->id]);

        $response->assertRedirect(route('administration.user-group.index'));

    }

    public function test_destroy_writes_deleted_audit_log()
    {
        $right = Right::factory()->create();
        $userGroup = UserGroup::factory()->create(['name' => 'Gone Group']);
        $userGroup->rights()->attach($right);

        $this->actingAs($this->administratorUser)
            ->delete(route('administration.user-group.destroy', $userGroup->id));

        $log = $userGroup->auditLogs()->sole();
        $this->assertSame(AuditEvent::DELETED, $log->event);
        $this->assertSame('Gone Group', $log->old_values['name']);
        $this->assertSame([$right->id], $log->old_values['rights']);
        $this->assertNull($log->new_values);
        $this->assertSame($this->administratorUser->id, $log->user_id);
    }

    public function test_history()
    {
        $rightGroup = RightGroup::factory()->create();
        [$oldRight, $newRight] = Right::factory(2)->for($rightGroup)->create()->all();
        $userGroup = UserGroup::factory()->create(['name' => 'Old Group']);
        $userGroup->rights()->attach($oldRight);
        $userGroup->saveLog(AuditEvent::CREATED, null, $userGroup->createSnapshot());

        $old = $userGroup->createSnapshot();
        $userGroup->update(['name' => 'New Group']);
        $userGroup->rights()->sync([$newRight->id]);
        $userGroup->saveLog(AuditEvent::UPDATED, $old, $userGroup->createSnapshot());

        $response = $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.history', $userGroup->id));

        $this->assertAdministrationRoute(['administration.user-group.history', $userGroup->id], 'administration/user-group/user-group-index');
        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
            ->component('administration/user-group/user-group-index')
            ->where('modal.component', 'administration/user-group/user-group-view-history')
            ->where('modal.props.userGroup.id', $userGroup->id)
            ->has('modal.props.rightGroups', 1)
            ->has('modal.props.versions', 2)
            ->where('modal.props.versions.0.event', AuditEvent::CREATED->value)
            ->where('modal.props.versions.0.userGroup.name', 'Old Group')
            ->has('modal.props.versions.0.userGroup.rights', 1)
            ->where('modal.props.versions.0.userGroup.rights.0.id', $oldRight->id)
            ->where('modal.props.versions.1.event', AuditEvent::UPDATED->value)
            ->where('modal.props.versions.1.userGroup.name', 'New Group')
            ->has('modal.props.versions.1.userGroup.rights', 1)
            ->where('modal.props.versions.1.userGroup.rights.0.id', $newRight->id)
            ->where('modal.props.versions.1.changes', ['name', 'rights'])
        );
    }

    public function test_history_does_not_write_to_the_database()
    {
        $userGroup = UserGroup::factory()->create();
        $userGroup->rights()->attach(Right::factory(2)->create());
        $userGroup->saveLog(AuditEvent::CREATED, null, $userGroup->createSnapshot());

        $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.history', $userGroup->id))
            ->assertOk();

        $this->assertDatabaseCount('user_group_rights', 2);
        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_history_returns_404_for_nonexistent_user_group()
    {
        $this->actingAs($this->administratorUser)
            ->get(route('administration.user-group.history', 999))
            ->assertNotFound();
    }
}
