import {router} from "@inertiajs/react";
import {HistoryDialog} from "@/components/history-dialog";
import {EvaluationTypes, GameEvaluationPermissions, PlayerEvaluationPermissions} from "@/enums";
import {useHasRight} from "@/hooks/use-has-right";
import { t } from '@/locale/translate';
import EvaluationView from "@/pages/evaluation/evaluation-view";
import gameEvaluationRoute from "@/routes/evaluation/game";
import playerEvaluationRoute from "@/routes/evaluation/player";
import type {Evaluation, EvaluationVersion} from "@/types/evaluation/evaluation";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";
import {useUser} from "@/hooks/use-auth";

type Props = {
    evaluation: Evaluation,
    versions: EvaluationVersion[],
    evaluationCriteriaGroups: EvaluationCriteriaGroups[]
}
export default function EvaluationViewHistory({evaluation, versions, evaluationCriteriaGroups}: Props) {
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
    const currentUserIsCreator = (evaluation.creator.id ?? null) === useUser().id;
    const canEdit = useHasRight(evaluationPermissions.EditAll) || (useHasRight(evaluationPermissions.Edit) && currentUserIsCreator);

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

    return (
        <HistoryDialog versions={versions} fields={fields} showUser={canViewCreator} canRestore={canEdit} onRestore={restoreVersion}>
            {version => <EvaluationView evaluation={version.evaluation} evaluationCriteriaGroups={evaluationCriteriaGroups}/>}
        </HistoryDialog>
    );
}
