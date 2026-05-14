<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserGroupRequest;
use App\Models\Right;
use App\Models\RightGroup;
use App\Models\UserGroup;

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

        return redirect()->route('administration.user-group.index');
    }

    public function show()
    {
        abort(404);
    }

    public function edit(int $id)
    {
        $rightGroups = RightGroup::with('rights:id,right_group_id,name,description')->get();
        $userGroup = UserGroup::findOrFail($id)->load('rights');

        return inertia('administration/user-group/user-group-edit', [
            'userGroup' => $userGroup,
            'rightGroups' => $rightGroups
        ]);
    }

    public function update(UserGroupRequest $request, UserGroup $userGroup)
    {
    }

    public function destroy(UserGroup $userGroup)
    {
    }
}
