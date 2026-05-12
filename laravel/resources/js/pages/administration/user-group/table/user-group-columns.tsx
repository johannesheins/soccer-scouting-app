import {ColumnDef} from "@tanstack/react-table";
import {UserGroup} from "@/types/types";
import sortHeader from "@/components/table/table-header-sort";
import {UserGroupRowActions} from "./user-group-row-actions";

export const userGroupColumns: ColumnDef<UserGroup>[] = [
    {
        accessorKey: "name",
        header: sortHeader("Name"),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "numberOfUsers",
        header: "Anzahl Benutzer",
        cell: ({row}) => <div className="font-medium">{row.getValue("numberOfUsers")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <UserGroupRowActions userGroup={row.original}/>,
    },
]
