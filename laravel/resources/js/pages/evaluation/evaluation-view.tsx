import {Evaluation, EvaluationCriteriaGroups} from "@/types/types";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Separator} from "@/components/ui/separator";
import player from "@/routes/player";
import {date} from "@/locale/date-locale";
import {MAX_SCORE, ScoreBar} from "@/components/score-bar";
import React from "react";
import {Badge} from "@/components/ui/badge";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import {ScoreCalculationService} from "@/services/score-calculation-service";
import ScoreDisplay from "@/components/score-display";

type Props = {
    evaluation: Evaluation
    evaluationCriteriaGroups: EvaluationCriteriaGroups[]
}
export default function EvaluationView({evaluation, evaluationCriteriaGroups}: Props) {
    const canViewCreator = useHasRight(RightEnum.EvaluationViewCreator);

    const scores: number[] = []
    evaluation.criteria_scores.forEach(criteria => {
        scores[criteria.evaluation_criteria_id] = criteria.score
    })

    const calculateScores = new ScoreCalculationService(scores, evaluationCriteriaGroups);

    return (
        <Card className="border-none shadow-none py-1 w-full">
            <CardHeader className="pb-2">
                <CardTitle className='grid grid-cols-[3fr_1fr]'>
                    <p className="text-2xl">{evaluation.home_team.clubname} - {evaluation.away_team.clubname}</p>
                    <p className="text-2xl text-end">{calculateScores.getTotalScore()} Punkte</p>
                </CardTitle>
                <div className="grid grid-cols-2 text-muted-foreground text-sm">
                    <p>
                        {date(evaluation?.kickoff_date)} {evaluation.kickoff_time.slice(0, 5)} Uhr
                    </p>
                    <a href={player.show.url(evaluation.player.id)} className="text-end">
                        {evaluation.player.firstname} {evaluation?.player.lastname}
                    </a>
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
