import type {Club, Player, Position} from "@/types/types";
import {Dialog, DialogContent, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import PlayerSearchForm from "@/pages/player/player-search-form";
import {DataTable} from "@/components/table/data-table";
import {playerSelectColumns} from "@/pages/player/table/player-columns";
import React, {useState} from "react";
import {Button} from "@/components/ui/button";
export default function PlayerSearchDialog({positions, clubs}: {positions: Position[]; clubs: Club[]}) {
    const [players, setPlayers] = useState<Player[]>([]);

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="outline">Spieler wählen</Button>
            </DialogTrigger>
            <DialogTitle>
                Spielersuche
            </DialogTitle>
            <DialogContent variant={"large"}>
                <div className="relative min-h-screen rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm positions={positions} clubs={clubs} returnData={true} onResponse={setPlayers}/>
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={playerSelectColumns} data={players} textOnEmpty={'Kein Spieler gefunden'}/>
                </div>
            </DialogContent>
        </Dialog>
    );
}
