import type {ColumnDef} from "@tanstack/react-table";
import sortHeader from "@/components/table/table-header-sort";
import { t } from '@/locale/translate';
import type {EvaluationCriteriaGroup} from "@/types/evaluation-criteria";
import {EvaluationCriteriaGroupRowActions} from "./evaluation-criteria-group-row-actions";

export const evaluationCriteriaGroupColumns: ColumnDef<EvaluationCriteriaGroup>[] = [
    {
        accessorKey: "name",
        header: sortHeader(() => t('Name')),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "evaluation_criteria_count",
        header: sortHeader(() => t('Criteria')),
        cell: ({row}) => <div className="font-medium">{row.getValue("evaluation_criteria_count")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <EvaluationCriteriaGroupRowActions group={row.original}/>,
    },
]