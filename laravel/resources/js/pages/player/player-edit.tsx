import { setLayoutProps } from '@inertiajs/react';
import { t } from '@/locale/translate';
import player from '@/routes/player';
import { usePreviousUrl } from '@/hooks/use-previous-url';
import PlayerForm from "@/components/from/player-form";

export default function PlayerEdit() {
    const previousUrl = usePreviousUrl();
    const usePrevious: boolean = previousUrl?.startsWith(player.search.url()) ?? false

    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Players'),
                href: player.index(),
            },
            {
                title: t('Search players'),
                href: usePrevious ? previousUrl : player.search()
            },
            {
                title: t('Edit player')
            },
        ],
    });

    return <PlayerForm edit backHref={usePrevious ? previousUrl : player.search.url()}/>;
}
