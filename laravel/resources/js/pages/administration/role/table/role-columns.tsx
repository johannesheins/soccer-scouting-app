import {ColumnDef} from "@tanstack/react-table";
import {Role} from "@/types/types";
import sortHeader from "@/components/table/table-header-sort";
import {RoleRowActions} from "./role-row-actions";

export const roleColumns: ColumnDef<Role>[] = [
    {
        accessorKey: "name",
        header: sortHeader("Name"),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "numberOfUsers",
        header: "Anzahl Benutzer",
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <RoleRowActions role={row.original}/>,
    },
]
