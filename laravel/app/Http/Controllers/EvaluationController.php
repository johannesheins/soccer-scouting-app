<?php

namespace App\Http\Controllers;

use App\DTOs\EvaluationSearchDTO;
use App\Enums\Request\PlayerRequestNameEnum as Name;
use App\Http\Requests\Evaluation\EvaluationCreateRequest;
use App\Http\Requests\Evaluation\EvaluationSearchRequest;
use App\Http\Requests\Evaluation\EvaluationStoreRequest;
use App\Models\Club;
use App\Models\Evaluation;
use App\Models\EvaluationCriteriaGroup;
use App\Models\EvaluationCriteriaScore;
use App\Models\Player;
use App\Models\Position;
use App\Models\Recommendation;
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
            new Middleware('can:create,App\Models\Evaluation', only: ['create', 'store']),
            new Middleware('can:view,evaluation', only: ['show']),
            new Middleware('can:update,evaluation', only: ['edit', 'update']),
            new Middleware('can:delete,evaluation', only: ['destroy']),
        ];
    }

    public function index()
    {
        return inertia('evaluation/evaluation-index');
    }

    public function create(EvaluationCreateRequest $request)
    {
        $playerId = $request->input(reqN(Name::playerId));
        if($playerId !== null){
            $player = Player::find($playerId)->loadForPlayerView();
        }

        return inertia('evaluation/evaluation-create', [
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'positions' => Position::with('positionGroup:id,name')->orderBy('id')->get(['id', 'position_code', 'position_group_id']),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),
            'recommendations' => Recommendation::all(),
            'player' => $player ?? null,
        ]);
    }

    public function store(EvaluationStoreRequest $request)
    {
        $data = $request->validated();
        $evaluation = Evaluation::create(
            $data + ['created_by' => auth()->id()],
        );

        foreach ($data['criteriaScores'] as $criteriaScore) {
            EvaluationCriteriaScore::create(
                $criteriaScore + ['evaluation_id' => $evaluation->id]
            );
        }

        return redirect()->route('evaluation.index');
    }

    public function show(Evaluation $evaluation)
    {
        return new Modal('evaluation/evaluation-show', [
            'evaluation' => $evaluation->loadForEvaluationView(),
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
        ])->baseRoute('evaluation.index');
    }

    public function edit(Evaluation $evaluation)
    {

    }

    public function update(EvaluationStoreRequest $request, Evaluation $evaluation)
    {

    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluation.index');
    }

    public function search(EvaluationSearchRequest $request)
    {
        $validated = $request->validated();

        $evaluationSearchDTO = new EvaluationSearchDTO($request->validated());
        $evaluationSearchService = new EvaluationSearchService();
        $evaluations = $evaluationSearchService->searchEvaluations($evaluationSearchDTO, $request->user(), [
            'player',
            'homeTeam',
            'awayTeam',
            'criteriaScores',
            'creator'
        ])->toArray();

        return inertia('evaluation/evaluation-search', [
            'evaluationCriteriaGroups' => EvaluationCriteriaGroup::with('evaluationCriteria')->get(),
            'players' => Player::allForSelectOption()->toArray(),
            'clubs' => Club::orderBy('clubname')->get(['id', 'clubname']),

            'queryParams' => $validated,
            'evaluations' => $evaluations,
        ]);
    }
}
