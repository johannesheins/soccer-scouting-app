import {UserRoundPlus} from "lucide-react";
import React, {useState} from "react";
import {PlayerFormDialog} from "@/components/from/player-form";
import {Button} from "@/components/ui/button";
import {Dialog, DialogContent, DialogTitle, DialogTrigger} from "@/components/ui/dialog";
import type { Player} from "@/types/player";

type Props = {
    onCreatedPlayer?: (player: Player) => void,
};

export default function PlayerCreateDialog({onCreatedPlayer}: Props){
    const [open, setOpen] = useState(false);

    function handleCreatedPlayer(player: Player) {
        onCreatedPlayer?.(player);
        setOpen(false);
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline"><UserRoundPlus/></Button>
            </DialogTrigger>
            <DialogContent variant="large">
                <DialogTitle>Spielersuche</DialogTitle>

                <PlayerFormDialog onSelectPlayer={handleCreatedPlayer}/>
            </DialogContent>
        </Dialog>
    )
}
