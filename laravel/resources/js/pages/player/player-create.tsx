import { setLayoutProps } from '@inertiajs/react';
import PlayerForm from "@/components/from/player-form";
import { useLastVisitedUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import player from "@/routes/player";

export default function PlayerCreate() {
    const searchUrl = useLastVisitedUrl(player.search.url());

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Player search'),
                href: searchUrl,
            },
            {
                title: t('Create player'),
            },
        ],
    });

    return <PlayerForm />;
}
