"use client"

import {ColumnDef} from "@tanstack/react-table"
import {Club, Player, Position} from "@/types/types";
import {PlayerRowActions} from "./player-row-actions";
import sortHeader from "@/components/table/table-header-sort";
import {PlayerSelectRowActions} from "@/pages/player/table/player-select-row-actions";
import months from "@/constants/months";

const firstname:ColumnDef<Player> = {
    accessorKey: "firstname",
    header: sortHeader("Vorname"),
    cell: ({row}) => <div className="font-medium">{row.getValue("firstname")}</div>,
};

const lastname:ColumnDef<Player> = {
        accessorKey: "lastname",
        header: sortHeader("Nachname"),
        cell: ({row}) => <div className="font-medium">{row.getValue("lastname")}</div>,
};

const monthOfBirth:ColumnDef<Player> = {
    accessorKey: "month_of_birth",
    header: sortHeader("Monat"),
    cell: ({row}) => <div className="font-medium">{months[Number(row.getValue("month_of_birth"))]}</div>,
};

const yearOfBirth:ColumnDef<Player> = {
    accessorKey: "year_of_birth",
    header: sortHeader("Jahrgang"),
    cell: ({row}) => <div className="font-medium">{row.getValue("year_of_birth")}</div>,
};

const club:ColumnDef<Player> = {
    accessorKey: "club",
    header: sortHeader("Verein"),
    sortingFn: (a, b) => {
        const ca: Club = a.getValue("club")
        const cb: Club = b.getValue("club")
        return ca.clubname.localeCompare(cb.clubname)
    },
    cell: ({row}) => {
        const club: Club = row.getValue("club")
        return <div className="font-medium">{club.clubname}</div>
    },
};

const positions:ColumnDef<Player> = {
    accessorKey: "positions",
    header: "Positionen",
    cell: ({ row }) => {
        const positions: Position[] = row.getValue('positions') ?? []
        return <div className="font-medium">{positions.map(p => p.position_code).join(', ')}</div>
    },
};

export const playerColumns: ColumnDef<Player>[] = [
    firstname,
    lastname,
    monthOfBirth,
    yearOfBirth,
    club,
    positions,
    {
        id: "actions",
        cell: ({row}) => <PlayerRowActions player={row.original}/>,
    },
];

export function playerSelectColumns(onSelect: (player: Player) => void): ColumnDef<Player>[] {
    return [
        firstname,
        lastname,
        monthOfBirth,
        yearOfBirth,
        club,
        positions,
        {
            id: "actions",
            cell: ({row}) => <PlayerSelectRowActions player={row.original} onClick={onSelect}/>,
        },
    ];
}
