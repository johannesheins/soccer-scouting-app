import {Head, Link} from '@inertiajs/react';
import {ShieldPlus, Search} from 'lucide-react';
import AccessGuard from "@/components/access-guard";
import {RightEnum} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import club from "@/routes/club";

export default function ClubIndex() {
    const canCreate = useHasRight(RightEnum.ClubCreate);
    const canSearch = useHasRight(RightEnum.ClubSearch);

    return (
        <>
            <Head title="Verein" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreate} title="Keine Berechtigung">
                            <Link href={club.create()} title="Verein erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <ShieldPlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Verein erstellen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title="Keine Berechtigung">
                            <Link href={club.search()} title="Verein suchen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <Search className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Verein suchen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                </div>
            </div>
        </>
    );
}

ClubIndex.layout = {
    breadcrumbs: [
        {
            title: 'Verein',
            href: club.index(),
        },
    ],
};
