<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationCriteriaRequest;
use App\Models\EvaluationCriteria;

class EvaluationCriteriaController extends Controller
{
    public function index()
    {
        return inertia('administration/evaluation-criteria/evaluation-criteria-index', [
            'evaluation_criteria' => EvaluationCriteria::all(),
        ]);
    }

    public function create(){
        return inertia('administration/evaluation-criteria/evaluation-criteria-create')
    }

    public function store(EvaluationCriteriaRequest $request)
    {
        return EvaluationCriteria::create($request->validated());
    }

    public function show(EvaluationCriteria $evaluationCriterion)
    {
        return $evaluationCriterion;
    }

    public function update(EvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->update($request->validated());

        return $evaluationCriterion;
    }

    public function destroy(EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->delete();

        return response()->json();
    }
}
