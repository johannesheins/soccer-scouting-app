import { setLayoutProps } from '@inertiajs/react';
import PlayerForm from "@/components/from/player-form";
import { useLastVisitedUrl } from '@/hooks/use-previous-url';
import { t } from '@/locale/translate';
import player from '@/routes/player';

export default function PlayerEdit() {
    const searchUrl = useLastVisitedUrl(player.search.url());

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Player'),
                href: searchUrl
            },
            {
                title: t('Edit player')
            },
        ],
    });

    return <PlayerForm edit backHref={searchUrl}/>;
}
