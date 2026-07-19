"use client"

import {ColumnDef} from "@tanstack/react-table"
import {Club, Evaluation, PlayerSmall} from "@/types/types";
import sortHeader from "@/components/table/table-header-sort";
import {format} from "date-fns";
import {de} from "date-fns/locale";
import EvaluationRowActions from "@/pages/evaluation/table/evaluation-row-actions";

const player:ColumnDef<Evaluation> = {
    accessorKey: "player",
    header: sortHeader("Spieler"),
    cell: ({row}) => {
        const player: PlayerSmall = row.getValue("player");
        return <div className="font-medium">{player.firstname} {player.lastname}</div>
    },
};

const homeTeam:ColumnDef<Evaluation> = {
    accessorKey: "home_team",
    header: sortHeader("Heimverein"),
    sortingFn: (a, b) => {
        const ca: Club = a.getValue("home_team")
        const cb: Club = b.getValue("home_team")
        return ca.clubname.localeCompare(cb.clubname)
    },
    cell: ({row}) => {
        const club: Club = row.getValue("home_team")
        return <div className="font-medium">{club.clubname}</div>
    },
};

const awayTeam:ColumnDef<Evaluation> = {
    accessorKey: "away_team",
    header: sortHeader("Gastverein"),
    sortingFn: (a, b) => {
        const ca: Club = a.getValue("away_team")
        const cb: Club = b.getValue("away_team")
        return ca.clubname.localeCompare(cb.clubname)
    },
    cell: ({row}) => {
        const club: Club = row.getValue("away_team")
        return <div className="font-medium">{club.clubname}</div>
    },
};

const kickoffDate:ColumnDef<Evaluation> = {
    accessorKey: "kickoff_date",
    header: sortHeader("Anstoß"),
    cell: ({ row }) => {
        const date = format(new Date(row.getValue('kickoff_date')), "dd.MM.yyyy", {locale: de})
        const time = row.original.kickoff_time.slice(0, 5)
        return <div className="font-medium grid grid-cols-2 gap-2 text-center">
            <span>{date}</span>
            <span>{time} Uhr</span>
        </div>
    },
};

const score:ColumnDef<Evaluation> = {
    accessorKey: "score",
    header: sortHeader("Punkte"),
    cell: ({ row }) => {
        return <div className="font-medium">{row.getValue('score')}</div>
    },
};

export const evaluationColumns: ColumnDef<Evaluation>[] = [
    player,
    homeTeam,
    awayTeam,
    kickoffDate,
    score,
    {
        id: "actions",
        cell: ({row}) => <EvaluationRowActions evaluation={row.original}/>,
    },
];
