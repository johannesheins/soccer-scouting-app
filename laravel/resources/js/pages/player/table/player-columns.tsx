"use client"

import {ColumnDef} from "@tanstack/react-table"
import {Club, Player, Position} from "@/types/types";
import {PlayerRowActions} from "@/pages/player/table/player-column-actions";
import {Button} from "@/components/ui/button";
import {ArrowUpDown, ArrowUp, ArrowDown} from "lucide-react";

function sortHeader(label: string) {
    return ({column}: { column: any }) => {
        const sorted = column.getIsSorted()
        const Icon = sorted === "asc" ? ArrowUp : sorted === "desc" ? ArrowDown : ArrowUpDown
        return (
            <Button variant="ghost" onClick={() => column.toggleSorting(sorted === "asc")}>
                {label}
                <Icon className="ml-2 h-4 w-4"/>
            </Button>
        )
    }
}

export const playerColumns: ColumnDef<Player>[] = [
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
        accessorKey: "year_of_birth",
        header: sortHeader("Jahrgang"),
        cell: ({row}) => <div className="font-medium">{row.getValue("year_of_birth")}</div>,
    },
    {
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
        cell: ({row}) => <PlayerRowActions player={row.original}/>,
    },
]
