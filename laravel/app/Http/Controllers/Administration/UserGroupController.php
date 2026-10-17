<?php

namespace App\Http\Controllers\Administration;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserGroupRequest;
use App\Models\AuditLog;
use App\Models\Right;
use App\Models\RightGroup;
use App\Models\UserGroup;
use App\Http\Responses\Modal;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public function index()
    {
        $userGroups = UserGroup::withCount('members as number_of_users')->get();

        return inertia('administration/user-group/user-group-index', [
            'userGroups' => $userGroups
        ]);
    }

    public function create()
    {
        return inertia('administration/user-group/user-group-create', [
            'rightGroups' => RightGroup::with('rights:id,right_group_id,name,description')->get()
        ]);
    }

    public function store(UserGroupRequest $request)
    {
        $validated = $request->validated();

        $userGroup = UserGroup::create(['name' => $validated['name']]);
        $userGroup->rights()->attach($validated['rights']);

        $userGroup->saveLog(AuditEvent::CREATED, null, $userGroup->createSnapshot());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User group was created.')]);
        return redirect()->route('administration.user-group.index');
    }

    public function show(UserGroup $userGroup)
    {
        abort(404);
    }

    public function edit(UserGroup $userGroup)
    {
        $rightGroups = RightGroup::with('rights:id,right_group_id,name,description')->get();
        $userGroup->load('rights');

        return inertia('administration/user-group/user-group-edit', [
            'userGroup' => $userGroup,
            'rightGroups' => $rightGroups
        ]);
    }

    public function update(UserGroupRequest $request, UserGroup $userGroup)
    {
        $validated = $request->validated();

        $old = $userGroup->createSnapshot();

        $userGroup->update(['name' => $validated['name']]);
        $userGroup->rights()->sync($validated['rights']);

        $userGroup->saveLog(AuditEvent::UPDATED, $old, $userGroup->createSnapshot());

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('User group was updated.')])->back();
    }

    public function destroy(UserGroup $userGroup)
    {
        $old = $userGroup->createSnapshot();
        $userGroup->delete();

        $userGroup->saveLog(AuditEvent::DELETED, $old, null);

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('User group was deleted.')])->back();
    }

    public function history(UserGroup $userGroup)
    {
        $rights = Right::all()->keyBy('id');

        $versions = $userGroup->auditLogs()->with('user')->orderBy('id')->get()
            ->map(function (AuditLog $log) use ($userGroup, $rights): array {
                $values = $log->new_values ?? $log->old_values;

                $version = $userGroup->newInstance([], true);
                $version->setRawAttributes(Arr::except($values, 'rights'), true);
                $version->setRelation('rights', $rights->only($values['rights'] ?? [])->values());

                return [
                    'id' => $log->id,
                    'event' => $log->event,
                    'created_at' => $log->created_at,
                    'user' => $log->user,
                    'changes' => $log->changedFields(),
                    'new_values' => $log->new_values,
                    'userGroup' => $version,
                ];
            })->all();

        return new Modal('administration/user-group/user-group-view-history', [
            'userGroup' => $userGroup,
            'versions' => $versions,
            'rightGroups' => RightGroup::with('rights:id,right_group_id,name,description')->get(),
        ])->baseRoute('administration.user-group.index');
    }
}
