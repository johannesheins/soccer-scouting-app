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
        return inertia('administration/user-group/user-group-index', [
            'userGroups' => UserGroup::all()
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
        dd($request->validated());
    }

    public function show(UserGroup $userGroup)
    {
    }

    public function update(UserGroupRequest $request, UserGroup $userGroup)
    {
    }

    public function destroy(UserGroup $userGroup)
    {
    }
}
