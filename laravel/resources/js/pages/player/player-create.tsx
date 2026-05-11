import PlayerForm from "@/components/player-form";
import player from "@/routes/player";

export default function PlayerCreate() {
    return <PlayerForm />;
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player.index.url(),
        },
        {
            title: 'Spieler erstellen',
        },
    ],
};
