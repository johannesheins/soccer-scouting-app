<?php

namespace App\Http\Controllers\Administration;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserCreateRequest;
use App\Http\Requests\Administration\UserUpdateRequest;
use App\Models\User;
use App\Models\UserGroup;
use Inertia\Inertia;

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
        $user->markPasswordAsExpired();
        $user->userGroups()->attach($validated['user_groups'] ?? []);

        $user->saveLog(AuditEvent::CREATED, null, $user->createSnapshot());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User was created.')]);
        return redirect()->route('administration.user.index');
    }

    public function show(User $user)
    {
        abort(404);
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

        $old = $user->createSnapshot();

        $user->update($validated);
        $user->userGroups()->sync($validated['user_groups'] ?? []);

        $user->saveLog(AuditEvent::UPDATED, $old, $user->createSnapshot());

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('User was updated.')])->back();
    }

    public function destroy(User $user)
    {
        $old = $user->createSnapshot();
        $user->delete();

        $user->saveLog(AuditEvent::DELETED, $old, null);

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('User was deleted.')])->back();
    }
}
