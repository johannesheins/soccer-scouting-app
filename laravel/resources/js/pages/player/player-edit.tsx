import { setLayoutProps } from '@inertiajs/react';
import { player } from '@/routes';
import PlayerForm from '@/components/player-form';
import { usePreviousUrl } from '@/hooks/use-previous-url';

export default function PlayerEdit() {
    const previousUrl = usePreviousUrl();
    const usePrevious: boolean = previousUrl?.startsWith(player.url()+'/search') ?? false

    setLayoutProps({
        breadcrumbs: [
            {
                title: 'Spieler',
                href: player()
            },
            {
                title: 'Spieler suchen',
                href: usePrevious ? previousUrl : player.url()+'/search'
            },
            {
                title: 'Spieler bearbeiten'
            },
        ],
    });

    return <PlayerForm edit />;
}
