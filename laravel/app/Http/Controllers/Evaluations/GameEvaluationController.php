<?php

namespace App\Http\Controllers\Evaluations;

use App\Enums\EvaluationTypes;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use App\Http\Controllers\Controller;
use App\Http\Requests\Evaluation\EvaluationCreateRequest;
use App\Http\Requests\Evaluation\GameEvaluationStoreRequest;
use App\Models\Club;
use App\Models\EvaluationCriteriaGroup;
use App\Models\GameEvaluation;
use App\Models\Player;
use App\Models\Position;
use App\Models\Recommendation;
use App\Services\EvaluationService;
use Emargareten\InertiaModal\Modal;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class GameEvaluationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,App\Models\GameEvaluation', only: ['create', 'store']),
            new Middleware('can:view,game_evaluation', only: ['show']),
            new Middleware('can:update,game_evaluation', only: ['edit', 'update']),
            new Middleware('can:delete,game_evaluation', only: ['destroy']),
        ];
    }

    public function index()
    {
        return redirect()->route('evaluation.index');
    }

    public function create(EvaluationCreateRequest $request)
    {
        $playerId = $request->input(reqN(Name::playerId));
        if ($playerId !== null) {
            $player = Player::find($playerId)->loadForPlayerView();
        }

        return inertia('evaluation/game/game-evaluation-create', [
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'recommendations' => Recommendation::all(),
            'player' => $player ?? null,
            'type' => EvaluationTypes::GAME->value,
        ]);
    }

    public function store(GameEvaluationStoreRequest $request)
    {
        EvaluationService::create(new GameEvaluation(), $request->validated());

        return redirect()->route('evaluation.index');
    }

    public function show(GameEvaluation $gameEvaluation)
    {
        return new Modal('evaluation/evaluation-show', [
            'evaluation' => $gameEvaluation->loadForView(),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
        ])->baseRoute('evaluation.index');
    }

    public function edit(GameEvaluation $gameEvaluation)
    {
        return inertia('evaluation/game/game-evaluation-edit', [
            'evaluation' => $gameEvaluation->load('player', 'criteriaScores'),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'recommendations' => Recommendation::all(),
            'player' => $gameEvaluation->player->loadForPlayerView(),
        ]);
    }

    public function update(GameEvaluationStoreRequest $request, GameEvaluation $gameEvaluation)
    {
        EvaluationService::update($gameEvaluation, $request->validated());

        return redirect()->route('evaluation.index');
    }

    public function destroy(GameEvaluation $gameEvaluation)
    {
        $gameEvaluation->delete();

        return redirect()->route('evaluation.index');
    }
}
