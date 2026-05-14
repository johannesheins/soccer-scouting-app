"use client"

import {ColumnDef} from "@tanstack/react-table"
import sortHeader from "@/components/table/table-header-sort";
import {User} from "@/types";
import {UserRowActions} from "@/pages/administration/user/table/user-row-actions";

export const userColumns: ColumnDef<User>[] = [
    {
        accessorKey: "firstname",
        header: sortHeader("Vorname"),
        cell: ({row}) => <div className="font-medium">{row.getValue("firstname")}</div>,
    },
    {
        accessorKey: "lastname",
        header: sortHeader("Nachname"),
        cell: ({row}) => <div className="font-medium">{row.getValue("lastname")}</div>,
    },
    {
        accessorKey: "email",
        header: sortHeader("E-Mail"),
        cell: ({row}) => <div className="font-medium">{row.getValue("email")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <UserRowActions user={row.original}/>,
    },
]
