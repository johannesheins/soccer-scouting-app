import {Head, Link} from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import {UserRoundPlus, UserSearch} from 'lucide-react';
import {player} from '@/routes';

export default function PlayerDashboard() {
    return (
        <>
            <Head title="Spieler" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href="/player/create" title="Spieler erstellen" className="flex flex-col justify-center items-center h-full">
                            <UserRoundPlus className={"size-10 icon-color"}/>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href="/player/search" title="Spieler suchen" className="flex flex-col justify-center items-center h-full">
                            <UserSearch className={"size-10 icon-color"}/>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>
                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </>
    );
}

PlayerDashboard.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
    ],
};
