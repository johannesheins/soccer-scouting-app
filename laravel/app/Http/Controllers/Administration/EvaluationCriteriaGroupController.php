<?php

namespace App\Http\Controllers\Administration;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationCriteriaGroupRequest;
use App\Models\EvaluationCriteriaGroup;

class EvaluationCriteriaGroupController extends Controller
{
    public function index()
    {
        return inertia('administration/evaluation-criteria-group/evaluation-criteria-group-index', [
            'evaluation_criteria_groups' => EvaluationCriteriaGroup::withCount('evaluationCriteria')->get(),
        ]);
    }

    public function create()
    {
        return inertia('administration/evaluation-criteria-group/evaluation-criteria-group-create');
    }

    public function store(EvaluationCriteriaGroupRequest $request)
    {
        $evaluationCriteriaGroup = EvaluationCriteriaGroup::create($request->validated());

        $evaluationCriteriaGroup->saveLog(AuditEvent::CREATED, null, $evaluationCriteriaGroup->createSnapshot());

        return redirect()->route('evaluation-criteria-group.index');
    }

    public function show(EvaluationCriteriaGroup $evaluationCriteriaGroup)
    {
        abort(404);
    }

    public function edit(EvaluationCriteriaGroup $evaluationCriteriaGroup)
    {
        return inertia('administration/evaluation-criteria-group/evaluation-criteria-group-edit', [
            'evaluationCriteriaGroup' => $evaluationCriteriaGroup,
        ]);
    }

    public function update(EvaluationCriteriaGroupRequest $request, EvaluationCriteriaGroup $evaluationCriteriaGroup)
    {
        $old = $evaluationCriteriaGroup->createSnapshot();

        $evaluationCriteriaGroup->update($request->validated());

        $evaluationCriteriaGroup->saveLog(AuditEvent::UPDATED, $old, $evaluationCriteriaGroup->createSnapshot());

        return redirect()->route('evaluation-criteria-group.index');
    }

    public function destroy(EvaluationCriteriaGroup $evaluationCriteriaGroup)
    {
        $old = $evaluationCriteriaGroup->createSnapshot();
        $evaluationCriteriaGroup->delete();

        $evaluationCriteriaGroup->saveLog(AuditEvent::DELETED, $old, null);

        return redirect()->route('evaluation-criteria-group.index');
    }
}