<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\EvaluationCriteria;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EvaluationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,App\Models\Evaluation', only: ['index']),
            new Middleware('can:search,App\Models\Evaluation', only: ['search']),
            new Middleware('can:create,App\Models\Evaluation', only: ['create', 'store']),
            new Middleware('can:view,evaluation', only: ['show']),
            new Middleware('can:update,evaluation', only: ['edit', 'update']),
            new Middleware('can:delete,evaluation', only: ['destroy']),
        ];
    }

    public function index()
    {
        return inertia('evaluation/evaluation-index');
    }

    public function create()
    {
        return inertia('evaluation/evaluation-create', [
            'evaluationCriteria' => EvaluationCriteria::all(),
        ]);
    }

    public function store(EvaluationRequest $request)
    {
        dd($request);
    }

    public function show(Evaluation $evaluation)
    {

    }

    public function edit(Evaluation $evaluation)
    {

    }

    public function update(EvaluationRequest $request, Evaluation $evaluation)
    {

    }

    public function destroy(Evaluation $evaluation)
    {

    }

    public function search()
    {

    }
}
