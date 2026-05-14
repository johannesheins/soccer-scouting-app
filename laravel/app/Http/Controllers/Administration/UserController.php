<?php

namespace App\Http\Controllers\Administration;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserRequest;
use App\Models\User;
use App\Models\UserGroup;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where("is_administrator", "=", 0)->get();

        return inertia('administration/user/user-index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return inertia('administration/user/user-create', [
            'userGroups' => UserGroup::all()
        ]);
    }

    public function store(UserRequest $request)
    {
        dd($request->validated());
    }

    public function show(User $user)
    {

    }

    public function edit(User $user)
    {

    }

    public function update(User $user)
    {

    }

    public function destroy(User $user)
    {

    }
}
