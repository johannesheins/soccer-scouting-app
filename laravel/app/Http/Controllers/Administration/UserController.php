<?php

namespace App\Http\Controllers\Administration;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UserRequest;
use App\Models\User;

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

    }

    public function store(UserRequest $request)
    {

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
