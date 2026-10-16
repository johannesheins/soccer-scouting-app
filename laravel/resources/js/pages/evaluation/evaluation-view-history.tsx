import {History, ListClock} from "lucide-react";
import {useState} from "react";
import {Badge} from "@/components/ui/badge";
import {Button} from "@/components/ui/button";
import {Dialog, DialogContent, DialogTitle} from "@/components/ui/dialog";
import {Separator} from "@/components/ui/separator";
import {EvaluationTypes, GameEvaluationPermissions, PlayerEvaluationPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import {useModal} from "@/lib/inertia-modal";
import {date} from "@/locale/date-locale";
import { t } from '@/locale/translate';
import EvaluationView from "@/pages/evaluation/evaluation-view";
import type {Evaluation, EvaluationVersion} from "@/types/evaluation/evaluation";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";
import gameEvaluationRoute from "@/routes/evaluation/game";
import playerEvaluationRoute from "@/routes/evaluation/player";
import {router} from "@inertiajs/react";

type Props = {
    evaluation: Evaluation,
    versions: EvaluationVersion[],
    evaluationCriteriaGroups: EvaluationCriteriaGroups[]
}
export default function EvaluationViewHistory({evaluation, versions, evaluationCriteriaGroups}: Props) {
    const { close } = useModal();
    const newestFirst = [...versions].reverse();
    const [selectedId, setSelectedId] = useState(newestFirst[0]?.id);
    const selected = newestFirst.find(version => version.id === selectedId);
    const [historyOpen, setHistoryOpen] = useState(false);

    const selectVersion = (id: number) => {
        setSelectedId(id);
        setHistoryOpen(false);
    };

    const isGameEvaluation = evaluation.evaluation_type === 'game';
    const isPlayerEvaluation = evaluation.evaluation_type === 'player';

    let evaluationPermissions = null;
    let evaluationRoute = null;
    switch (evaluation.evaluation_type) {
        case EvaluationTypes.GAME:
            evaluationPermissions = GameEvaluationPermissions;
            evaluationRoute = gameEvaluationRoute;
            break;
        case EvaluationTypes.PLAYER:
            evaluationPermissions = PlayerEvaluationPermissions;
            evaluationRoute = playerEvaluationRoute;
            break;
        default:
            throw new Error(`Unknown evaluation type: ${evaluation.evaluation_type}`);
    }

    const canViewCreator = useHasRight(evaluationPermissions.ViewCreator);

    const events: Record<EvaluationVersion['event'], string> = {
        created: t('Created'),
        updated: t('Updated'),
        deleted: t('Deleted'),
    };

    const fields: Record<string, string> = {
        date: t('Date'),
        strengths: t('Strengths'),
        weaknesses: t('Weaknesses'),
        comment: t('Comment'),
        recommendation_id: t('Recommendation'),
        criteriaScores: t('Evaluation criteria'),
        player_id: t('Player'),
        home_club_id: t('Home club'),
        home_team: t('Home team'),
        guest_club_id: t('Away club'),
        guest_team: t('Away team'),
    };

    const restoreVersion = (version: EvaluationVersion) => {
        router.put(evaluationRoute.update.url(evaluation.id), version.new_values);
    };

    const changedFields = (version: EvaluationVersion) => version.changes.length > 0 && (
        <span className="flex flex-wrap gap-1">
            {version.changes.filter(field => fields[field]).map(field => (
                <Badge key={field} variant="outline">{fields[field]}</Badge>
            ))}
        </span>
    );

    const versionList = (
        <nav className="grid gap-2 content-start">
            {newestFirst.map(version => (
                <div key={version.id} className="grid">
                    <Button variant={version.id === selectedId ? 'secondary' : 'ghost'} onClick={() => selectVersion(version.id)}
                        className="col-start-1 row-start-1 h-auto flex-col items-start gap-1 whitespace-normal text-start pe-12"
                    >
                        <span className="font-medium">{date(version.created_at, 'Pp')}</span>
                        <span className="text-muted-foreground text-xs">{events[version.event]} {canViewCreator && version.user && `- ${version.user.firstname} ${version.user.lastname}`}</span>
                        {changedFields(version)}
                    </Button>
                    <Button
                        className="col-start-1 row-start-1 justify-self-end mt-1 me-1"
                        title={t('Restore version')}
                        variant="ghost"
                        onClick={() => restoreVersion(version)}
                        hidden={newestFirst[0].id === version.id}
                    >
                        <History />
                    </Button>
                </div>
            ))}
        </nav>
    );

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogContent variant="extra-large" className="overflow-hidden" aria-describedby={undefined}>
                <DialogTitle className="sr-only">{t('Change history')}</DialogTitle>
                {versions.length <= 0 && <p className="p-5">{t('No changes recorded')}</p>}
                {versions.length > 0 && (
                    <div className="grid grid-rows-[auto_1fr] md:grid-rows-1 md:grid-cols-[1fr_auto_3fr] gap-4 h-[calc(90vh-4rem)] sm:h-[calc(80vh-4rem)]">
                        <Button variant="outline" className="justify-start md:hidden" onClick={() => setHistoryOpen(true)}>
                            <ListClock />
                            {selected && <span>{events[selected.event]} · {date(selected.created_at, 'Pp')}</span>}
                        </Button>
                        {historyOpen && (
                            <div className="absolute inset-0 z-10 rounded-lg overflow-hidden md:hidden">
                                <div className="absolute inset-0 bg-black/50 animate-in fade-in-0" onClick={() => setHistoryOpen(false)}/>
                                <div className="absolute inset-y-0 left-0 w-3/4 max-w-sm grid grid-rows-[auto_1fr] gap-4 p-4 bg-background border-r animate-in slide-in-from-left">
                                    <p className="font-semibold">{t('Change history')}</p>
                                    <div className="min-h-0 overflow-y-auto">{versionList}</div>
                                </div>
                            </div>
                        )}

                        <div className="hidden md:block md:min-h-0 md:overflow-y-auto">{versionList}</div>

                        <Separator orientation="vertical" className="hidden md:block"/>

                        <div className="min-h-0 overflow-y-auto">
                            {selected && (
                                <EvaluationView evaluation={selected.evaluation} evaluationCriteriaGroups={evaluationCriteriaGroups}/>
                            )}
                        </div>
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
