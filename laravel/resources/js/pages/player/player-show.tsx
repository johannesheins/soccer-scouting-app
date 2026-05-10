import { useModal } from '@/lib/inertia-modal';
import {Dialog, DialogContent, DialogTitle} from '@/components/ui/dialog';
import { PlayerView } from '@/pages/player/player-view';
import type { Player } from '@/types/types';

export default function PlayerShow({ player }: { player: Player }) {
    const { close } = useModal();

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogTitle>
                {player.firstname} {player.lastname}
            </DialogTitle>
            <DialogContent>
                <PlayerView player={player} />
            </DialogContent>
        </Dialog>
    );
}
