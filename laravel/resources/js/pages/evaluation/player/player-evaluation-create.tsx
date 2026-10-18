import { setLayoutProps } from '@inertiajs/react';
import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import evaluation from "@/routes/evaluation";
import playerEvaluation from "@/routes/evaluation/player";

export default function PlayerEvaluationCreate() {
    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Evaluations'),
                href: evaluation.search.url(),
            },
            {
                title: t('Create internal player evaluation'),
            },
        ],
    });

    return <EvaluationForm type="player" route={playerEvaluation}/>;
}
