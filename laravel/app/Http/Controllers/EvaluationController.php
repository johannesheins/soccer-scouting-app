<?php

namespace App\Http\Controllers;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\EvaluationTypes;
use App\Http\Requests\Evaluation\EvaluationSearchRequest;
use App\Models\Club;
use App\Models\Evaluation;
use App\Models\EvaluationCriteriaGroup;
use App\Models\Player;
use App\Services\EvaluationSearchService;
use Emargareten\InertiaModal\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EvaluationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:index,App\Models\Evaluation', only: ['index']),
            new Middleware('can:search,App\Models\Evaluation', only: ['search']),
        ];
    }

    public function index()
    {
        return inertia('evaluation/evaluation-index');
    }

    public function search(EvaluationSearchRequest $request)
    {
        $validated = $request->validated();

        $evaluationSearchDTO = new EvaluationSearchDTO($request->validated());
        $evaluationSearchService = new EvaluationSearchService();
        $evaluations = $evaluationSearchService->searchEvaluations(
            dto: $evaluationSearchDTO,
            user: $request->user(),
            with: [
                'criteriaScores',
                'creator',
            ],
            withFor: [
                EvaluationTypes::GAME->value => [
                    'player',
                    'homeTeam',
                    'guestTeam'
                ],
                EvaluationTypes::PLAYER->value => [
                    'player',
                ],
            ]
        );

        return inertia('evaluation/evaluation-search', [
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'players' => Player::allForSelectOption()->toArray(),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),

            'queryParams' => $validated,
            'evaluations' => $evaluations->toArray(),
        ]);
    }
}
