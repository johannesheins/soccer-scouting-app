"use client"

import type {ColumnDef} from "@tanstack/react-table"
import sortHeader from "@/components/table/table-header-sort";
import { t } from '@/locale/translate';
import {UserRowActions} from "@/pages/administration/user/table/user-row-actions";
import type {User} from "@/types";

export const userColumns: ColumnDef<User>[] = [
    {
        accessorKey: "firstname",
        header: sortHeader(() => t('First name')),
        cell: ({row}) => <div className="font-medium">{row.getValue("firstname")}</div>,
    },
    {
        accessorKey: "lastname",
        header: sortHeader(() => t('Last name')),
        cell: ({row}) => <div className="font-medium">{row.getValue("lastname")}</div>,
    },
    {
        accessorKey: "email",
        header: sortHeader(() => t('Email')),
        cell: ({row}) => <div className="font-medium">{row.getValue("email")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <UserRowActions user={row.original}/>,
    },
]
