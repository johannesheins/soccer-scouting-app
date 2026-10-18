<?php

namespace App\Http\Controllers\Evaluations;

use App\Enums\EvaluationTypes;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use App\Http\Controllers\Controller;
use App\Http\Requests\Evaluation\EvaluationCreateRequest;
use App\Http\Requests\Evaluation\PlayerEvaluationStoreRequest;
use App\Models\Club;
use App\Models\EvaluationCriteriaGroup;
use App\Models\Player;
use App\Models\PlayerEvaluation;
use App\Models\Position;
use App\Models\Recommendation;
use App\Services\EvaluationService;
use App\Http\Responses\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class PlayerEvaluationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,App\Models\PlayerEvaluation', only: ['create', 'store']),
            new Middleware('can:view,player_evaluation', only: ['show']),
            new Middleware('can:update,player_evaluation', only: ['edit', 'update']),
            new Middleware('can:delete,player_evaluation', only: ['destroy']),
            new Middleware('can:viewHistory,player_evaluation', only: ['history']),
        ];
    }

    public function create(EvaluationCreateRequest $request)
    {
        $playerId = $request->input(reqN(Name::playerId));
        if ($playerId !== null) {
            $player = Player::find($playerId)->loadForView();
        }

        return inertia('evaluation/player/player-evaluation-create', [
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'recommendations' => Recommendation::all(),
            'player' => $player ?? null,
            'type' => EvaluationTypes::PLAYER->value,
        ]);
    }

    public function store(PlayerEvaluationStoreRequest $request)
    {
        EvaluationService::create(new PlayerEvaluation(), $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Evaluation was created.')]);
        return to_route('evaluation.search');
    }

    public function show(PlayerEvaluation $playerEvaluation)
    {
        return new Modal('evaluation/evaluation-show', [
            'evaluation' => $playerEvaluation->loadForView(),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
        ])->baseRoute('evaluation.search');
    }

    public function edit(PlayerEvaluation $playerEvaluation)
    {
        return inertia('evaluation/player/player-evaluation-edit', [
            'evaluation' => $playerEvaluation->load('player', 'criteriaScores'),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'recommendations' => Recommendation::all(),
            'player' => $playerEvaluation->player->loadForView(),
        ]);
    }

    public function update(PlayerEvaluationStoreRequest $request, PlayerEvaluation $playerEvaluation)
    {
        EvaluationService::update($playerEvaluation, $request->validated());

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('Evaluation was updated.')])->back();
    }

    public function destroy(PlayerEvaluation $playerEvaluation)
    {
        EvaluationService::delete($playerEvaluation);

        return Inertia::flash('toast', ['type' => 'success', 'message' => __('Evaluation was deleted.')])->back();
    }

    public function history(PlayerEvaluation $playerEvaluation)
    {
        return new Modal('evaluation/evaluation-view-history', [
            'evaluation' => $playerEvaluation->loadForView(),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'versions' => EvaluationService::versions($playerEvaluation),
        ])->baseRoute('evaluation.search');
    }
}
