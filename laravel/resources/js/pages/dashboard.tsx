import {Head, Link, usePage} from '@inertiajs/react';
import AccessGuard from "@/components/access-guard";
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import {PlayerPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import {cn} from "@/lib/utils";
import { t } from '@/locale/translate';
import { dashboard } from '@/routes';
import player from "@/routes/player";
import dashboardSettings from '@/routes/settings/dashboard';
import type {playerQuickSearchUserClubs, PlayerQuickSearchUserYears} from "@/types/player";

type Props = { playerQuickSearchClubs: playerQuickSearchUserClubs, playerQuickSearchUserYears: PlayerQuickSearchUserYears };
export default function Dashboard() {
    const { playerQuickSearchClubs, playerQuickSearchUserYears } = usePage<Props>().props;
    const canSearch = useHasRight(PlayerPermissions.Search);

    const years = playerQuickSearchUserYears.map(year => (year.year_of_birth));

    const hasPlayerQuickSearchClubs = playerQuickSearchClubs.length > 0;
    const hasPlayerQuickSearchUserYears = playerQuickSearchUserYears.length > 0;

    return (
        <>
            <Head title={t('Home')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title={t('No permission')}>
                            <div className="grid grid-cols-1 gap-1 p-1 items-center h-full w-full">
                                {hasPlayerQuickSearchClubs ? (
                                    playerQuickSearchClubs.map((club) => (
                                        <Link key={club.id} href={generateUrlForPlayerSearch('club_ids[]', String(club.id))} title={t('Search players')}>
                                            <p className="text-icon-color font-bold text-center">{club.clubname}</p>
                                        </Link>
                                    ))
                                ) : (
                                    <Link href={dashboardSettings.index.url()}>
                                        <p className="text-icon-color font-bold text-center">{t('Pick up to three clubs for the player quick search')}</p>
                                    </Link>
                                )}
                            </div>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                        {t('Top 3 players')}
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title={t('No permission')}>
                            <div className={cn("grid gap-1 p-1 items-center h-full w-full", hasPlayerQuickSearchUserYears ? "grid-cols-2" : "grid-cols-1")}>
                                {hasPlayerQuickSearchUserYears ? (
                                    years.map((year) => (
                                        <Link key={year} href={generateUrlForPlayerSearch('years_of_birth[]', String(year))} title={t('Search players')} className="flex flex-col gap-2 justify-center items-center h-full">
                                            <p className="text-icon-color font-bold">{year}</p>
                                        </Link>
                                    ))
                                ) : (
                                    <Link href={dashboardSettings.index.url()}>
                                        <p className="text-icon-color font-bold text-center">{t('Pick up to six years of birth for the player quick search')}</p>
                                    </Link>
                                )}
                            </div>
                        </AccessGuard>
                    </div>
                </div>
                <div className="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </>
    );
}

function generateUrlForPlayerSearch(key: string, value: string): string {
    const params = new URLSearchParams();
    params.append(key, value);
    return `${player.search.url()}?${params.toString()}`;
}

Dashboard.layout = () => ({
    breadcrumbs: [
        {
            title: t('Home'),
            href: dashboard(),
        },
    ],
});
