import type {Club, Player, Position} from "@/types/types";
import {useModal} from "@/lib/inertia-modal";
import {Dialog, DialogContent, DialogTitle} from "@/components/ui/dialog";
import PlayerSearchForm from "@/pages/player/player-search-form";
import {DataTable} from "@/components/table/data-table";
import {playerSelectColumns} from "@/pages/player/table/player-columns";
import React from "react";
export default function PlayerSearchModal({positions, clubs, players}: {positions: Position[]; clubs: Club[], players: Player[]}) {
    const { close } = useModal();

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogTitle>
                Spielersuche
            </DialogTitle>
            <DialogContent>
                <div className="relative min-h-screen rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm positions={positions} clubs={clubs} modal/>
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl md:min-h-min dark:border-sidebar-border">
                    <DataTable columns={playerSelectColumns} data={players} textOnEmpty={'Kein Spieler gefunden'}/>
                </div>
            </DialogContent>
        </Dialog>
    );
}
