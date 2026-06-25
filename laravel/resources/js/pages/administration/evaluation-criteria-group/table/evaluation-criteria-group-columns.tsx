import {ColumnDef} from "@tanstack/react-table";
import {EvaluationCriteriaGroup} from "@/types/types";
import sortHeader from "@/components/table/table-header-sort";
import {EvaluationCriteriaGroupRowActions} from "./evaluation-criteria-group-row-actions";

export const evaluationCriteriaGroupColumns: ColumnDef<EvaluationCriteriaGroup>[] = [
    {
        accessorKey: "name",
        header: sortHeader("Name"),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "evaluation_criteria_count",
        header: sortHeader("Kriterien"),
        cell: ({row}) => <div className="font-medium">{row.getValue("evaluation_criteria_count")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <EvaluationCriteriaGroupRowActions group={row.original}/>,
    },
]