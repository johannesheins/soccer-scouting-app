import {Head, Link} from '@inertiajs/react';
import {UserRoundPlus, UserSearch} from 'lucide-react';
import PlayerSearchForm from "@/pages/player/player-search-form";
import player from "@/routes/player";

export default function PlayerIndex() {
    return (
        <>
            <Head title="Spieler" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={player.create()} title="Spieler erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                            <UserRoundPlus className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">Spieler erstellen</p>
                        </Link>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <Link href={player.search()} title="Spieler suchen" className="flex flex-col gap-2 justify-center items-center h-full">
                            <UserSearch className={"size-10 icon-color"}/>
                            <p className="text-icon-color font-bold">Spieler suchen</p>
                        </Link>
                    </div>
                </div>
                <div className="content-center invisible md:visible relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm />
                </div>
            </div>
        </>
    );
}

PlayerIndex.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player.index(),
        },
    ],
};
