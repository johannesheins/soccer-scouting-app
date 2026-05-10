<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\RoleRequest;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return inertia('administration/roles/index', [
            'roles' => Role::all()
        ]);
    }

    public function create()
    {

    }

    public function store(RoleRequest $request)
    {
    }

    public function show(Role $role)
    {
    }

    public function update(RoleRequest $request, Role $role)
    {
    }

    public function destroy(Role $role)
    {
    }
}
