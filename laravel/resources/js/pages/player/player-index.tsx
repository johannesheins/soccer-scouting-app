import {Head, Link, usePage} from '@inertiajs/react';
import {UserRoundPlus, UserSearch} from 'lucide-react';
import PlayerSearchForm from "@/pages/player/player-search-form";
import player from "@/routes/player";
import AccessGuard from "@/components/access-guard";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import type {Club, Position} from "@/types/types";

type Props = { positions: Position[]; clubs: Club[] };
export default function PlayerIndex() {
    const { positions, clubs } = usePage<Props>().props;
    const canCreate = useHasRight(RightEnum.PlayerCreate);
    const canSearch = useHasRight(RightEnum.PlayerSearch);

    return (
        <>
            <Head title="Spieler" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreate} title="Keine Berechtigung">
                            <Link href={player.create()} title="Spieler erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <UserRoundPlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Spieler erstellen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title="Keine Berechtigung">
                            <Link href={player.search()} title="Spieler suchen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <UserSearch className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Spieler suchen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                </div>
                {canSearch && <div className="content-center invisible md:visible relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerSearchForm positions={positions} clubs={clubs} />
                </div>}
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
