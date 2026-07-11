import type {Club, Player, Position} from "@/types/types";
import {Dialog, DialogContent, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import PlayerSearchForm from "@/pages/player/player-search-form";
import {DataTable} from "@/components/table/data-table";
import {playerColumns, playerSelectColumns} from "@/pages/player/table/player-columns";
import React, {useState} from "react";
import {Button} from "@/components/ui/button";
import {PlayerView} from "@/pages/player/player-view";
import PlayerCreateDialog from "@/pages/player/player-create-dialog";


type Props = {
    positions: Position[];
    clubs: Club[],
    selectPlayer?: boolean,
    value?: Player,
    onSelectedPlayer?: (player: Player) => void,
};
export default function PlayerSearchDialog({positions, clubs, selectPlayer, value, onSelectedPlayer}: Props) {
    const [open, setOpen] = useState(false);
    const [players, setPlayers] = useState<Player[]>([]);
    const [selectedPlayer, setSelectedPlayer] = useState<Player>();

    function handlePlayerSelected(player: Player) {
        setSelectedPlayer(player);
        onSelectedPlayer?.(player);
        setOpen(false);
    }

    const columns = selectPlayer === true ? playerSelectColumns(handlePlayerSelected) : playerColumns;

    const dialogTrigger = (
        <div className="grid grid-cols-[1fr_auto] gap-4">
            <DialogTrigger asChild>
                <Button variant="outline">Spieler {selectedPlayer ? 'ändern' : 'wählen'}</Button>
            </DialogTrigger>
            <PlayerCreateDialog onCreatedPlayer={handlePlayerSelected}/>
        </div>
    )

    if(selectedPlayer){
        value = selectedPlayer
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            {!value && dialogTrigger}
            {value && <PlayerView player={value} button={dialogTrigger}/>}

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
