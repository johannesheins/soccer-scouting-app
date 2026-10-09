import player from "@/routes/player";
import { t } from '@/locale/translate';
import PlayerForm from "@/components/from/player-form";

export default function PlayerCreate() {
    return <PlayerForm />;
}

PlayerCreate.layout = () => ({
    breadcrumbs: [
        {
            title: t('Players'),
            href: player.index(),
        },
        {
            title: t('Create player'),
        },
    ],
});
