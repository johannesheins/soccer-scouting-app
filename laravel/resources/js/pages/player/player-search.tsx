import player from "@/routes/player";
import {Head, usePage} from "@inertiajs/react";
import React from "react";
import type {Club, Player, Position} from "@/types/types";

import {DataTable} from "@/components/table/data-table";
import {playerColumns} from "@/pages/player/table/player-columns";
import PlayerSearchForm from "@/pages/player/player-search-form";

type Props = { positions: Position[]; clubs: Club[]; players: Player[]};

const playerRoute = player;
export default function PlayerSearch(){
    const { players } = usePage<Props>().props;

    return (
        <>
            <Head title="Spieler suchen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-screen rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm />
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={playerColumns} data={players} textOnEmpty={'Kein Spieler gefunden'}/>
                </div>
            </div>
        </>
    )
}

PlayerSearch.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: playerRoute.index.url(),
        },
        {
            title: 'Spieler suchen',
        },
    ],
};
