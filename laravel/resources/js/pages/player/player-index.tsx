import {Head, Link, usePage} from '@inertiajs/react';
import { t } from '@/locale/translate';
import {UserRoundPlus, UserSearch} from 'lucide-react';
import AccessGuard from "@/components/access-guard";
import {PlayerPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import PlayerSearchForm from "@/pages/player/player-search-form";
import player from "@/routes/player";
import type {Club} from "@/types/club";
import type {Position} from "@/types/position";

type Props = { positions: Position[]; clubs: Club[] };
export default function PlayerIndex() {
    const { positions, clubs } = usePage<Props>().props;
    const canCreate = useHasRight(PlayerPermissions.Create);
    const canSearch = useHasRight(PlayerPermissions.Search);

    return (
        <>
            <Head title={t('Players')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreate} title={t('No permission')}>
                            <Link href={player.create()} title={t('Create player')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <UserRoundPlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Create player')}</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title={t('No permission')}>
                            <Link href={player.search()} title={t('Search players')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <UserSearch className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Search players')}</p>
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

PlayerIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Players'),
            href: player.index(),
        },
    ],
});
