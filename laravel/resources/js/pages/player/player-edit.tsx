import { player } from '@/routes';
import PlayerForm from "@/components/player-form";

export default function PlayerEdit() {
    return PlayerForm(true);
}

PlayerEdit.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
        {
            title: 'Spieler bearbeiten',
        },
    ],
};
