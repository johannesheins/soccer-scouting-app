import {Head, Link, usePage} from '@inertiajs/react';
import {Plus} from "lucide-react";
import {DataTable} from "@/components/table/data-table";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";
import type {EvaluationCriteriaGroup} from "@/types/evaluation-criteria";
import {evaluationCriteriaGroupColumns} from "./table/evaluation-criteria-group-columns";

type Props = { evaluation_criteria_groups: EvaluationCriteriaGroup[] }

export default function EvaluationCriteriaGroupIndex() {
    const {evaluation_criteria_groups} = usePage<Props>().props;

    return (
        <>
            <Head title={t('Criteria groups')}/>
            <div className="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative aspect-video md:aspect-32/9 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <Link href={evaluationCriteriaGroup.create()} title={t('Create criteria group')}
                          className="flex flex-col gap-2 justify-center items-center h-full">
                        <Plus className={"size-10 icon-color"}/>
                        <p className="text-icon-color font-bold">{t('Create criteria group')}</p>
                    </Link>
                </div>
                <div className="content-center relative flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={evaluationCriteriaGroupColumns} data={evaluation_criteria_groups}
                               textOnEmpty={t('No criteria groups found.')}/>
                </div>
            </div>
        </>
    );
}

EvaluationCriteriaGroupIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('Criteria groups'),
        },
    ],
});