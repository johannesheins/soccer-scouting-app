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
        return inertia('administration/evaluation-criteria/evaluation-criteria-create');
    }

    public function store(EvaluationCriteriaRequest $request)
    {
        EvaluationCriteria::create($request->validated());

        return redirect()->route('evaluation-criteria.index');
    }

    public function show(EvaluationCriteria $evaluationCriterion)
    {
        abort(404);
    }

    public function edit(EvaluationCriteria $evaluationCriterion)
    {
        return inertia('administration/evaluation-criteria/evaluation-criteria-edit', [
            'evaluationCriterion' => $evaluationCriterion,
        ]);
    }

    public function update(EvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->update($request->validated());

        return redirect()->route('evaluation-criteria.index');
    }

    public function destroy(EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->delete();

        return redirect()->route('evaluation-criteria.index');
    }
}
