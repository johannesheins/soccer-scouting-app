<?php

namespace App\Services;

use App\Enums\AuditEvent;
use App\Interfaces\EvaluationInterface;
use App\Models\Evaluation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    public static function create(Evaluation $evaluation, array $data): EvaluationInterface
    {
        $data['created_by'] = auth()->id();

        $result = DB::transaction(function () use ($evaluation, $data): EvaluationInterface {
            $evaluation->fill(Arr::except($data, 'criteriaScores'))->save();

            $evaluation->criteriaScores()->createMany($data['criteriaScores']);

            return $evaluation;
        });

        $evaluation->saveLog(AuditEvent::CREATED, null, $evaluation->createSnapshot());

        return $result;
    }

    public static function update(Evaluation $evaluation, array $data): EvaluationInterface
    {
        $old = $evaluation->createSnapshot();

        $result = DB::transaction(function () use ($evaluation, $data): EvaluationInterface {
            $evaluation->update(Arr::except($data, ['criteriaScores', 'created_by']));

            $evaluation->criteriaScores()->delete();
            $evaluation->criteriaScores()->createMany($data['criteriaScores']);

            return $evaluation;
        });

        $evaluation->saveLog(AuditEvent::UPDATED, $old, $evaluation->createSnapshot());

        return $result;
    }

    public static function delete(Evaluation $evaluation): bool
    {
        $old = $evaluation->createSnapshot();
        $result = $evaluation->delete();

        $evaluation->saveLog(AuditEvent::DELETED, $old, null);

        return $result;
    }
}
