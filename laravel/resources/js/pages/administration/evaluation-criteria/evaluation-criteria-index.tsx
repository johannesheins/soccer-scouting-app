import {Head, Link, usePage} from '@inertiajs/react';
import {administration} from '@/routes';
import {DataTable} from "@/components/table/data-table";
import {evaluationCriteriaColumns} from "@/pages/administration/evaluation-criteria/table/evaluation-criteria-columns";
import {EvaluationCriteria} from "@/types/types";
import {Plus} from "lucide-react";
import evaluationCriteria from "@/routes/evaluation-criteria";

type Props = { evaluation_criteria: EvaluationCriteria[] }

export default function EvaluationCriteriaIndex() {
    const {evaluation_criteria} = usePage<Props>().props;

    return (
        <>
            <Head title="Bewertungskriterien"/>
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={evaluationCriteria.create()} title="Bewertungskriterium erstellen"
                          className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">Bewertungskriterium erstellen</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={evaluationCriteriaColumns} data={evaluation_criteria} textOnEmpty="Keine Bewertungskriterien gefunden." className={"h-full"}/>
                </div>
            </div>
        </>
    );
}

EvaluationCriteriaIndex.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Bewertungskriterien'
        }
    ],
};
