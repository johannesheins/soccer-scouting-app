import player from "@/routes/player";
import PlayerForm from "@/components/from/player-form";

export default function PlayerCreate() {
    return <PlayerForm />;
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player.index(),
        },
        {
            title: 'Spieler erstellen',
        },
    ],
};
