import {Head, usePage} from "@inertiajs/react";
import React from "react";
import {DataTable} from "@/components/table/data-table";
import { t } from '@/locale/translate';
import EvaluationSearchForm from "@/pages/evaluation/evaluation-search-form";
import {useEvaluationColumns} from "@/pages/evaluation/table/evaluation-columns";
import {ScoreCalculationService} from "@/services/score-calculation-service";
import type {Club} from "@/types/club";
import type {EvaluationSearchQuery} from "@/types/evaluation/evaluation-search-query";
import type {EvaluationSearchResult} from "@/types/evaluation/search";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";
import type {PlayerOption} from "@/types/player";

type Props = {
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    players: PlayerOption[],
    clubs: Club[];
    queryParams: EvaluationSearchQuery;
    evaluations: EvaluationSearchResult[];
}
export default function EvaluationSearch() {
    const { evaluationCriteriaGroups, players, clubs, queryParams, evaluations } = usePage<Props>().props;
    const evaluationColumns = useEvaluationColumns();

    evaluations.map(evaluation => {
        const scoreCalculation = new ScoreCalculationService(evaluation.criteria_scores, evaluationCriteriaGroups);
        scoreCalculation.calculate();

        evaluation.total_score = scoreCalculation.getTotalScore();
        evaluation.group_scores = scoreCalculation.getGroupScores();
    });

    return (
        <>
            <Head title={t('Search evaluations')} />
            <div className="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <EvaluationSearchForm
                        evaluationCriteriaGroups={evaluationCriteriaGroups}
                        players={players}
                        clubs={clubs}
                        queryParams={queryParams}
                    />
                </div>

                <div className="relative overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={evaluationColumns} data={evaluations} textOnEmpty={t('No evaluation found')}/>
                </div>
            </div>
        </>
    )
}
