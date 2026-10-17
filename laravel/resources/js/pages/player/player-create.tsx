import { setLayoutProps } from '@inertiajs/react';
import PlayerForm from "@/components/from/player-form";
import { t } from '@/locale/translate';
import player from "@/routes/player";

export default function PlayerCreate() {
    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Player'),
                href: player.search.url(),
            },
            {
                title: t('Create player'),
            },
        ],
    });

    return <PlayerForm />;
}
