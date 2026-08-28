"use client"

import type {ColumnDef} from "@tanstack/react-table"
import sortHeader from "@/components/table/table-header-sort";
import type {Club} from "@/types/types";
import {ClubRowActions} from "./club-row-actions";

const clubname:ColumnDef<Club> = {
    accessorKey: "clubname",
    header: sortHeader("Vereinsname"),
    cell: ({row}) => <div className="font-medium">{row.getValue("clubname")}</div>,
};

const zipCode:ColumnDef<Club> = {
    accessorKey: "zip_code",
    header: sortHeader("PLZ"),
    cell: ({row}) => <div className="font-medium">{row.getValue("zip_code")}</div>,
};

const city:ColumnDef<Club> = {
    accessorKey: "city",
    header: sortHeader("Stadt"),
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
