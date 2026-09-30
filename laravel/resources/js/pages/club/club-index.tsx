import {Head, Link} from '@inertiajs/react';
import {ShieldPlus, Search} from 'lucide-react';
import AccessGuard from "@/components/access-guard";
import {ClubPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import { t } from '@/locale/translate';
import club from "@/routes/club";

export default function ClubIndex() {
    const canCreate = useHasRight(ClubPermissions.Create);
    const canSearch = useHasRight(ClubPermissions.Search);

    return (
        <>
            <Head title={t('Clubs')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreate} title={t('No permission')}>
                            <Link href={club.create()} title={t('Create club')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <ShieldPlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Create club')}</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title={t('No permission')}>
                            <Link href={club.search()} title={t('Search clubs')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <Search className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Search clubs')}</p>
                            </Link>
                        </AccessGuard>
                    </div>
                </div>
            </div>
        </>
    );
}

ClubIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Clubs'),
            href: club.index(),
        },
    ],
});
