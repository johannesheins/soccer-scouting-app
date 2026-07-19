import {Head, usePage} from "@inertiajs/react";
import {DataTable} from "@/components/table/data-table";
import React from "react";
import EvaluationSearchForm from "@/pages/evaluation/evaluation-search-form";
import {evaluationColumns} from "@/pages/evaluation/table/evaluation-columns";
import {Club, Evaluation, EvaluationCriteriaGroups} from "@/types/types";

type Props = {
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    clubs: Club[];
    evaluations: Evaluation[];
}
export default function EvaluationSearch() {
    const { evaluationCriteriaGroups, clubs, evaluations } = usePage<Props>().props;

    return (
        <>
            <Head title="Spieler suchen" />
            <div className="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <EvaluationSearchForm evaluationCriteriaGroups={evaluationCriteriaGroups} clubs={clubs}/>
                </div>

                <div className="relative overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={evaluationColumns} data={evaluations} textOnEmpty={'Kein Bewertung gefunden'}/>
                </div>
            </div>
        </>
    )
}
