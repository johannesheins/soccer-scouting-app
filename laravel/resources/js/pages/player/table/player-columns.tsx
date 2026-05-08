"use client"

import { ColumnDef } from "@tanstack/react-table"
import {Club, Player, Position} from "@/types/types";
import {PlayerRowActions} from "@/pages/player/table/player-column-actions";

export const playerColumns: ColumnDef<Player>[] = [
    {
        accessorKey: "firstname",
        header: "Vorname",
        cell: ({ row }) => {
            const firstname = String(row.getValue("firstname"))

            return <div className="font-medium">{firstname}</div>
        },
    },
    {
        accessorKey: "lastname",
        header: "Nachname",
        cell: ({ row }) => {
            const lastname = String(row.getValue("lastname"))

            return <div className="font-medium">{lastname}</div>
        },
    },
    {
        accessorKey: "age",
        header: "Alter",
        cell: ({ row }) => {
            const age = parseInt(row.getValue("age"))

            return <div className="font-medium">{age}</div>
        },
    },
    {
        accessorKey: "club",
        header: "Verein",
        cell: ({ row }) => {
            const club: Club = row.getValue('club')

            return <div className="font-medium">{club.clubname}</div>
        },
    },
    {
        accessorKey: "positions",
        header: "Positionen",
        cell: ({ row }) => {
            const positions: Position[] = row.getValue('positions') ?? []

            return <div className="font-medium">{positions.map(p => p.position_code).join(', ')}</div>
        },
    },
    {
        id: "actions",
        cell: ({ row }) => <PlayerRowActions player={row.original} />,
    },
]
