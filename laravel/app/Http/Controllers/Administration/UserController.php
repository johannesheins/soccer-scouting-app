<?php

namespace App\Http\Controllers\Administration;

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
        $validated = $request->validated();

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->userGroups()->attach($validated['userGroups'] ?? []);

        return redirect(route('administration.user.index'));
    }

    public function show(User $user)
    {

    }

    public function edit(User $user)
    {
        $user->load('userGroups');

        return inertia('administration/user/user-edit', [
            'user' => $user,
            'userGroups' => UserGroup::all()
        ]);
    }

    public function update(User $user)
    {

    }

    public function destroy(User $user)
    {

    }
}
