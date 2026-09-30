"use client"

import type {ColumnDef} from "@tanstack/react-table"
import sortHeader from "@/components/table/table-header-sort";
import { t } from '@/locale/translate';
import type {Club} from "@/types/club";
import {ClubRowActions} from "./club-row-actions";

const clubname:ColumnDef<Club> = {
    accessorKey: "clubname",
    header: sortHeader(() => t('Club name')),
    cell: ({row}) => <div className="font-medium">{row.getValue("clubname")}</div>,
};

const zipCode:ColumnDef<Club> = {
    accessorKey: "zip_code",
    header: sortHeader(() => t('Postcode')),
    cell: ({row}) => <div className="font-medium">{row.getValue("zip_code")}</div>,
};

const city:ColumnDef<Club> = {
    accessorKey: "city",
    header: sortHeader(() => t('City')),
    cell: ({row}) => <div className="font-medium">{row.getValue("city")}</div>,
};

export const clubColumns: ColumnDef<Club>[] = [
    clubname,
    zipCode,
    city,
    {
        id: "actions",
        cell: ({row}) => <ClubRowActions club={row.original}/>,
    },
];
