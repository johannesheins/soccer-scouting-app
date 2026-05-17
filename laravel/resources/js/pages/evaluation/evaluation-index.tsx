import {Head, Link} from '@inertiajs/react';
import {FilePlus, FileSearch} from 'lucide-react';
import player from "@/routes/player";
import AccessGuard from "@/components/access-guard";
import {useHasRight} from "@/hooks/use-has-right";
import {RightEnum} from "@/enums";
import {PlaceholderPattern} from "@/components/ui/placeholder-pattern";
import evaluation from "@/routes/evaluation";

export default function EvaluationIndex() {
    const canCreate = useHasRight(RightEnum.EvaluationCreate);
    const canSearch = useHasRight(RightEnum.EvaluationSearch);

    return (
        <>
            <Head title="Spieler" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreate} title="Keine Berechtigung">
                            <Link href={evaluation.create()} title="Spieler-Bewertung erstellen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <FilePlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Spieler-Bewertung erstellen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title="Keine Berechtigung">
                            <Link href={evaluation.search()} title="Spieler-Bewertung suchen" className="flex flex-col gap-2 justify-center items-center h-full">
                                <FileSearch className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">Spieler-Bewertung suchen</p>
                            </Link>
                        </AccessGuard>
                    </div>
                </div>
                <div className="content-center invisible md:visible relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
        </>
    );
}

EvaluationIndex.layout = {
    breadcrumbs: [
        {
            title: 'Spieler-Bewertungen',
            href: player.index(),
        },
    ],
};
