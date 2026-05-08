"use client"

import { ColumnDef } from "@tanstack/react-table"
import {Club, Player, Position} from "@/types/types";
import { MoreHorizontal } from "lucide-react"
import { Button } from "@/components/ui/button"
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {viewPlayer} from "@/pages/player/table/player-column-actions";

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
        cell: ({ row }) => {
            const player = row.original

            return (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" className="h-8 w-8 p-0">
                            <span className="sr-only">Menü öffnen</span>
                            <MoreHorizontal className="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>
                            Aktionen
                        </DropdownMenuLabel>
                        <DropdownMenuItem onClick={() =>viewPlayer(player.id)}>
                            Spieler ansehen
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            Spieler bearbeiten
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            Spieler löschen
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )
        },
    },
]
