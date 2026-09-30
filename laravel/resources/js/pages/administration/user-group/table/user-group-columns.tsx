import type {ColumnDef} from "@tanstack/react-table";
import sortHeader from "@/components/table/table-header-sort";
import { t } from '@/locale/translate';
import type {UserGroup} from "@/types/user-group";
import {UserGroupRowActions} from "./user-group-row-actions";

export const userGroupColumns: ColumnDef<UserGroup>[] = [
    {
        accessorKey: "name",
        header: sortHeader(() => t('Name')),
        cell: ({row}) => <div className="font-medium">{row.getValue("name")}</div>,
    },
    {
        accessorKey: "number_of_users",
        header: () => t('Number of users'),
        cell: ({row}) => <div className="font-medium">{row.getValue("number_of_users")}</div>,
    },
    {
        id: "actions",
        cell: ({row}) => <UserGroupRowActions userGroup={row.original}/>,
    },
]
