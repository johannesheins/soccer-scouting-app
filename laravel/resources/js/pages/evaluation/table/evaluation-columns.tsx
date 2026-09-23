"use client"

import type {ColumnDef} from "@tanstack/react-table"
import sortHeader from "@/components/table/table-header-sort";
import {GameEvaluationPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import {date} from "@/locale/date-locale";
import EvaluationRowActions from "@/pages/evaluation/table/evaluation-row-actions";
import type {User} from "@/types/auth";
import type {Club} from "@/types/club";
import type {Evaluation} from "@/types/evaluation/evaluation";
import type {PlayerSmall} from "@/types/player";

const player:ColumnDef<Evaluation> = {
    accessorKey: "player",
    header: sortHeader("Spieler"),
    cell: ({row}) => {
        const player: PlayerSmall = row.getValue("player");
        return <div className="font-medium">{player.firstname} {player.lastname}</div>
    },
};

const homeClub:ColumnDef<Evaluation> = {
    accessorKey: "home_club",
    header: sortHeader("Heimverein"),
    sortingFn: (a, b) => {
        const ca: Club = a.getValue("home_club")
        const cb: Club = b.getValue("home_club")
        return ca?.clubname.localeCompare(cb?.clubname)
    },
    cell: ({row}) => {
        const club: Club = row.getValue("home_club")
        return <div className="font-medium">{club?.clubname}</div>
    },
};

const guestClub:ColumnDef<Evaluation> = {
    accessorKey: "guest_club",
    header: sortHeader("Gastverein"),
    sortingFn: (a, b) => {
        const ca: Club = a.getValue("guest_club")
        const cb: Club = b.getValue("guest_club")
        return ca?.clubname.localeCompare(cb?.clubname)
    },
    cell: ({row}) => {
        const club: Club = row.getValue("guest_club")
        return <div className="font-medium">{club?.clubname}</div>
    },
};

const kickoffDate:ColumnDef<Evaluation> = {
    accessorKey: "date",
    header: sortHeader("Datum"),
    cell: ({ row }) => {
        const d = date(row.getValue('date'))
        return <div className="font-medium">{d}</div>
    },
};

const score:ColumnDef<Evaluation> = {
    accessorKey: "total_score",
    header: sortHeader("Punkte"),
    cell: ({ row }) => {
        return <div className="font-medium">{row.getValue('total_score')}</div>
    },
};

const creator:ColumnDef<Evaluation> = {
    accessorKey: "creator",
    header: sortHeader("Autor"),
    cell: ({ row }) => {
        const creator: User = row.getValue('creator');
        return <div className="font-medium">{creator.firstname} {creator.lastname}</div>
    },
};

export function useEvaluationColumns(): ColumnDef<Evaluation>[] {
    const canViewCreator = useHasRight(GameEvaluationPermissions.ViewCreator);

    return [
        player,
        homeClub,
        guestClub,
        kickoffDate,
        score,
        ...(canViewCreator ? [creator] : []),
        {
            id: "actions",
            cell: ({row}) => <EvaluationRowActions evaluation={row.original}/>,
        },
    ];
}
