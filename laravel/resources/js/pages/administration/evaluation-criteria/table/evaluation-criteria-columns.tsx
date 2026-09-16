import type {ColumnDef} from "@tanstack/react-table";
import sortHeader from "@/components/table/table-header-sort";
import type {EvaluationCriteria} from "@/types/evaluation-criteria";
import {EvaluationCriteriaRowActions} from "./evaluation-criteria-row-actions";

export const evaluationCriteriaColumns: ColumnDef<EvaluationCriteria>[] = [
    {
        accessorKey: "name",
        header: sortHeader("Name"),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        id: "group",
        accessorFn: row => row.group?.name ?? '',
        header: sortHeader("Gruppe"),
        cell: ({row}) => <div className="font-medium">{row.original.group?.name ?? '—'}</div>,
    },
    {
        accessorKey: "minimum_player_age",
        header: sortHeader("Mindestalter"),
        cell: ({row}) => <div className="font-medium">{row.getValue("minimum_player_age")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <EvaluationCriteriaRowActions criterion={row.original}/>,
    },
]
