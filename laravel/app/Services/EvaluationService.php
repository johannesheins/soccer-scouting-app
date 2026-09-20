<?php

namespace App\Services;

use App\Interfaces\EvaluationInterface;
use App\Models\Evaluation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    public static function create(EvaluationInterface $evaluation, array $data): EvaluationInterface
    {
        $data['created_by'] = auth()->id();

        return DB::transaction(function () use ($evaluation, $data): EvaluationInterface {
            $evaluation->fill(Arr::except($data, 'criteriaScores'))->save();

            $evaluation->criteriaScores()->createMany($data['criteriaScores']);

            return $evaluation;
        });
    }

    public static function update(Evaluation $evaluation, array $data): EvaluationInterface
    {
        return DB::transaction(function () use ($evaluation, $data): EvaluationInterface {
            $evaluation->update(Arr::except($data, ['criteriaScores', 'created_by']));

            $evaluation->criteriaScores()->delete();
            $evaluation->criteriaScores()->createMany($data['criteriaScores']);

            return $evaluation;
        });
    }
}
