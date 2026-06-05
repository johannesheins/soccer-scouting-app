<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationCriteriaRequest;
use App\Models\EvaluationCriteria;

class EvaluationCriteriaController extends Controller
{
    public function index()
    {
        return EvaluationCriteria::all();
    }

    public function store(EvaluationCriteriaRequest $request)
    {
        return EvaluationCriteria::create($request->validated());
    }

    public function show(EvaluationCriteria $evaluationCriteria)
    {
        return $evaluationCriteria;
    }

    public function update(EvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriteria)
    {
        $evaluationCriteria->update($request->validated());

        return $evaluationCriteria;
    }

    public function destroy(EvaluationCriteria $evaluationCriteria)
    {
        $evaluationCriteria->delete();

        return response()->json();
    }
}
