<?php

namespace App\Http\Controllers\Administration;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationCriteriaRequest;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaGroup;

class EvaluationCriteriaController extends Controller
{
    public function index()
    {
        return inertia('administration/evaluation-criteria/evaluation-criteria-index', [
            'evaluation_criteria' => EvaluationCriteria::with('group')->get(),
        ]);
    }

    public function create()
    {
        return inertia('administration/evaluation-criteria/evaluation-criteria-create', [
            'evaluation_criteria_groups' => EvaluationCriteriaGroup::all(),
        ]);
    }

    public function store(EvaluationCriteriaRequest $request)
    {
        $evaluationCriterion = EvaluationCriteria::create($request->validated());

        $evaluationCriterion->saveLog(AuditEvent::CREATED, null, $evaluationCriterion->createSnapshot());

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
            'evaluation_criteria_groups' => EvaluationCriteriaGroup::all(),
        ]);
    }

    public function update(EvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriterion)
    {
        $old = $evaluationCriterion->createSnapshot();

        $evaluationCriterion->update($request->validated());

        $evaluationCriterion->saveLog(AuditEvent::UPDATED, $old, $evaluationCriterion->createSnapshot());

        return redirect()->route('evaluation-criteria.index');
    }

    public function destroy(EvaluationCriteria $evaluationCriterion)
    {
        $old = $evaluationCriterion->createSnapshot();
        $evaluationCriterion->delete();

        $evaluationCriterion->saveLog(AuditEvent::DELETED, $old, null);

        return redirect()->route('evaluation-criteria.index');
    }
}
