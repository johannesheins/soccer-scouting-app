<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserCreateRequest;
use App\Http\Requests\Administration\UserUpdateRequest;
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

    public function store(UserCreateRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->userGroups()->attach($validated['user_groups'] ?? []);

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

    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);
        $user->userGroups()->sync($validated['user_groups'] ?? []);

        return redirect(route('administration.user.index'));
    }

    public function destroy(User $user)
    {

    }
}
