import React from "react";
import {MAX_SCORE, ScoreBar} from "@/components/score-bar";
import ScoreDisplay from "@/components/score-display";
import {Badge} from "@/components/ui/badge";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Separator} from "@/components/ui/separator";
import {GameEvaluationPermissions, PlayerEvaluationPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import {date} from "@/locale/date-locale";
import playerRoute from "@/routes/player";
import {ScoreCalculationService} from "@/services/score-calculation-service";
import type {Evaluation} from "@/types/evaluation/evaluation";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";
import {GameEvaluation} from "@/types/evaluation/game-evaluation";
import {PlayerEvaluation} from "@/types/evaluation/player-evaluation";

type Props = {
    evaluation: Evaluation
    evaluationCriteriaGroups: EvaluationCriteriaGroups[]
}
export default function EvaluationView({evaluation, evaluationCriteriaGroups}: Props) {
    const isGameEvaluation = evaluation.evaluation_type === 'game';
    const isPlayerEvaluation = evaluation.evaluation_type === 'player';

    const canViewCreator = (
        isGameEvaluation && useHasRight(GameEvaluationPermissions.ViewCreator)
        || isPlayerEvaluation && useHasRight(PlayerEvaluationPermissions.ViewCreator)
    );

    const gameEvaluation = isGameEvaluation ? (evaluation as GameEvaluation) : undefined;
    const playerEvaluation = isPlayerEvaluation ? (evaluation as PlayerEvaluation) : undefined;

    const player = gameEvaluation?.player ?? playerEvaluation?.player;

    const scores: number[] = []
    evaluation.criteria_scores.forEach(criteria => {
        scores[criteria.evaluation_criteria_id] = criteria.score
    })

    const calculateScores = new ScoreCalculationService(scores, evaluationCriteriaGroups);

    return (
        <Card className="border-none shadow-none py-1 w-full">
            <CardHeader className="pb-2">
                <CardTitle className='grid grid-cols-[3fr_1fr]'>
                    {isGameEvaluation && (
                        <p>{gameEvaluation?.home_club.clubname} - {gameEvaluation?.guest_club.clubname}</p>
                    )}
                    {isPlayerEvaluation && (
                        <a href={playerRoute.show.url(gameEvaluation?.player.id ?? playerEvaluation?.player.id ?? '')} className="text-2xl">
                            {player?.firstname} {player?.lastname}
                        </a>
                    )}
                    <p className="text-2xl text-end col-start-2">{calculateScores.getTotalScore()} Punkte</p>
                </CardTitle>
                <div className="grid grid-cols-2 text-muted-foreground text-sm">
                    <p>
                        {date(evaluation?.date)}
                    </p>
                    {isGameEvaluation && (
                        <a href={playerRoute.show.url(gameEvaluation?.player.id ?? playerEvaluation?.player.id ?? '')} className="text-end">
                            {player?.firstname} {player?.lastname}
                        </a>
                    )}
                </div>
            </CardHeader>

            <Separator/>

            <CardContent className="grid gap-4">
                {evaluationCriteriaGroups.length <= 0 && <p className="p-5">Keine Bewertungskriterien gefunden</p>}
                {evaluationCriteriaGroups.map(group => (
                    <div key={group.id}>
                        <div className="flex justify-between w-full font-medium text-muted-foreground uppercase ">
                            <span className="text-xs">{group.name}</span>
                            <ScoreDisplay currentValue={calculateScores.getGroupScore(group.id)} maxScore={MAX_SCORE * group.evaluation_criteria.length} />
                        </div>
                        <div className="grid sm:grid-cols-2 gap-4 mt-2 font-medium">
                            {group.evaluation_criteria.map(criteria => {
                                return (
                                    <div key={criteria.id}>
                                        <div className="grid grid-flow-col justify-between text-muted-foreground text-xs tracking-wide mb-1">
                                            <p className="uppercase text-foreground">{criteria.name}</p>
                                        </div>
                                        <ScoreBar disabled={true} value={scores[criteria.id] ?? 0}/>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </CardContent>

            <Separator/>

            <CardContent className="grid sm:grid-cols-2 gap-4">
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Stärken</p>
                    <p className="font-medium">{evaluation?.strengths ?? '-'}</p>
                </div>
                <div className="sm:col-start-2">
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Schwächen</p>
                    <p className="font-medium">{evaluation?.weaknesses ?? '-'}</p>
                </div>
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Bemerkung</p>
                    <p className="font-medium">{evaluation?.comment ?? '-'}</p>
                </div>
                <div className="sm:col-start-2">
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Empfehlung</p>
                    <Badge variant="secondary">{evaluation?.recommendation?.name ?? 'Keine Empfehlung gewählt'}</Badge>
                </div>
            </CardContent>

            <Separator/>

            <CardContent className="grid grid-flow-col gap-4">
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Zuletzt geändert</p>
                    <p className="font-medium">{date(evaluation.updated_at)}</p>
                </div>
                <div>
                    <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Erstellt am</p>
                    <p className="font-medium">{date(evaluation.created_at)}</p>
                </div>
                {canViewCreator && (
                    <div>
                        <p className="text-muted-foreground text-xs uppercase tracking-wide mb-1">Autor</p>
                        <p className="font-medium">{evaluation.creator.firstname} {evaluation.creator.lastname}</p>
                    </div>
                )}
            </CardContent>
        </Card>
    )
}
