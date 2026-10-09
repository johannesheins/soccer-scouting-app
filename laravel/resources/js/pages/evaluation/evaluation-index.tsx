import {Head, Link} from '@inertiajs/react';
import {FilePlus, FileSearch} from 'lucide-react';
import AccessGuard from "@/components/access-guard";
import {PlaceholderPattern} from "@/components/ui/placeholder-pattern";
import {EvaluationPermissions, GameEvaluationPermissions, PlayerEvaluationPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import { t } from '@/locale/translate';
import evaluation from "@/routes/evaluation";
import gameEvaluation from "@/routes/evaluation/game";
import playerEvaluation from "@/routes/evaluation/player";

export default function EvaluationIndex() {
    const canCreateGameEvaluation = useHasRight(GameEvaluationPermissions.Create);
    const canCreatePlayerEvaluation = useHasRight(PlayerEvaluationPermissions.Create);
    const canSearch = useHasRight(EvaluationPermissions.Search);

    return (
        <>
            <Head title={t('Evaluations')} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreateGameEvaluation} title={t('No permission')}>
                            <Link href={gameEvaluation.create()} title={t('Create match evaluation')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <FilePlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Create match evaluation')}</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canCreatePlayerEvaluation} title={t('No permission')}>
                            <Link href={playerEvaluation.create()} title={t('Create internal player evaluation')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <FilePlus className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Create internal player evaluation')}</p>
                            </Link>
                        </AccessGuard>
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <AccessGuard active={canSearch} title={t('No permission')}>
                            <Link href={evaluation.search()} title={t('Search evaluations')} className="flex flex-col gap-2 justify-center items-center h-full">
                                <FileSearch className={"size-10 icon-color"}/>
                                <p className="text-icon-color font-bold">{t('Search evaluations')}</p>
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

EvaluationIndex.layout = () => ({
    breadcrumbs: [
        {
            title: t('Evaluations'),
            href: evaluation.index(),
        },
    ],
});
