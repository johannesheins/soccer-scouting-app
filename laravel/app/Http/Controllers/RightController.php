<?php

namespace App\Http\Controllers;

use App\Http\Requests\RightRequest;
use App\Models\Right;

class RightController extends Controller
{
    public function index()
    {
        return Right::all();
    }

    public function store(RightRequest $request)
    {
        return Right::create($request->validated());
    }

    public function show(Right $right)
    {
        return $right;
    }

    public function update(RightRequest $request, Right $right)
    {
        $right->update($request->validated());

        return $right;
    }

    public function destroy(Right $right)
    {
        $right->delete();

        return response()->json();
    }
}
