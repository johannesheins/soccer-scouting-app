import { player } from '@/routes';
import PlayerForm from "@/components/player-form";

export default function PlayerCreate() {
    return <PlayerForm />;
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
        {
            title: 'Spieler erstellen',
        },
    ],
};
