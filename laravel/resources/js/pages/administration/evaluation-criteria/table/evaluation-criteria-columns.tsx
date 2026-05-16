import {ColumnDef} from "@tanstack/react-table";
import {EvaluationCriteria} from "@/types/types";
import sortHeader from "@/components/table/table-header-sort";
import {EvaluationCriteriaRowActions} from "./evaluation-criteria-row-actions";

export const evaluationCriteriaColumns: ColumnDef<EvaluationCriteria>[] = [
    {
        accessorKey: "name",
        header: sortHeader("Name"),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "minimum_player_age",
        header: "Mindestalter",
        cell: ({row}) => <div className="font-medium">{row.getValue("minimum_player_age")}</div>,
    },
    {
        accessorKey: "multiplier",
        header: "Multiplikator",
        cell: ({row}) => <div className="font-medium">{row.getValue("multiplier")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <EvaluationCriteriaRowActions criterion={row.original}/>,
    },
]