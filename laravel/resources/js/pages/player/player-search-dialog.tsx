import type {Club, Player, Position} from "@/types/types";
import {Dialog, DialogContent, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import PlayerSearchForm from "@/pages/player/player-search-form";
import {DataTable} from "@/components/table/data-table";
import {playerColumns, playerSelectColumns} from "@/pages/player/table/player-columns";
import React, {useEffect, useState} from "react";
import {Button} from "@/components/ui/button";
import {PlayerView} from "@/pages/player/player-view";


type Props = {
    positions: Position[];
    clubs: Club[],
    selectPlayer?: boolean,
    onSelectPlayer?: (player: Player) => void,
};
export default function PlayerSearchDialog({positions, clubs, selectPlayer, onSelectPlayer}: Props) {
    const [players, setPlayers] = useState<Player[]>([]);
    const [selectedPlayer, setSelectedPlayer] = useState<Player>();

    const columns = selectPlayer === true ? playerSelectColumns(setSelectedPlayer) : playerColumns;

    useEffect(() => {
        if (selectedPlayer && onSelectPlayer) {
            onSelectPlayer(selectedPlayer);
        }
    }, [selectedPlayer]);

    const dialogTrigger = (
        <DialogTrigger asChild>
            <Button variant="outline">Spieler {selectedPlayer ? 'ändern' : 'wählen'}</Button>
        </DialogTrigger>
    )

    return (
        <Dialog>
            {!selectedPlayer && dialogTrigger}
            {selectedPlayer && <PlayerView player={selectedPlayer} button={dialogTrigger}/>}

            <DialogContent variant="large">
                <DialogTitle>Spielersuche</DialogTitle>
                <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm positions={positions} clubs={clubs} returnData={true} onResponse={setPlayers}/>
                </div>

                <div className="relative overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={columns} data={players} textOnEmpty={'Kein Spieler gefunden'}/>
                </div>
            </DialogContent>
        </Dialog>
    );
}
