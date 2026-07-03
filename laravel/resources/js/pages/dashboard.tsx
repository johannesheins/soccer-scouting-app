import {Head, Link} from '@inertiajs/react';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import player from "@/routes/player";
import AccessGuard from "@/components/access-guard";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import {getYears} from "@/hooks/form-options";

export default function Dashboard() {
    const canSearch = useHasRight(RightEnum.PlayerSearch);

    const years = getYears(new Date().getFullYear() - 13, null, 6);

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                        Pinned Clubs
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                        Top 3 Player
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title="Keine Berechtigung">
                            <div className="grid grid-cols-2 gap-1 p-1 items-center h-full w-full">
                                {years.map((year) => (
                                    <Link href={generateUrlForPlayerSearch('years_of_birth[]', year)} title="Spieler mit Jahrgang suchen" className="flex flex-col gap-2 justify-center items-center h-full">
                                        <p className="text-icon-color font-bold">{year}</p>
                                    </Link>
                                ))}
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

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
