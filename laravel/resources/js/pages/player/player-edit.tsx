import { setLayoutProps } from '@inertiajs/react';
import player from '@/routes/player';
import PlayerForm from '@/components/player-form';
import { usePreviousUrl } from '@/hooks/use-previous-url';

export default function PlayerEdit() {
    const previousUrl = usePreviousUrl();
    const usePrevious: boolean = previousUrl?.startsWith(player.search.url()) ?? false

    setLayoutProps({
        breadcrumbs: [
            {
                title: 'Spieler',
                href: player.index(),
            },
            {
                title: 'Spieler suchen',
                href: usePrevious ? previousUrl : player.search()
            },
            {
                title: 'Spieler bearbeiten'
            },
        ],
    });

    return <PlayerForm edit backHref={usePrevious ? previousUrl : player.search.url()}/>;
}
