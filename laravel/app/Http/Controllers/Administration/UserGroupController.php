<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserGroupRequest;
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

    }

    public function store(UserGroupRequest $request)
    {
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
