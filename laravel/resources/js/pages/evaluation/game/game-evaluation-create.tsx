import { setLayoutProps } from '@inertiajs/react';
import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import evaluation from "@/routes/evaluation";
import gameEvaluation from "@/routes/evaluation/game";

export default function GameEvaluationCreate() {
    setLayoutProps({
        breadcrumbs: [
            {
                title: t('Evaluations'),
                href: evaluation.search.url(),
            },
            {
                title: t('Create match evaluation'),
            },
        ],
    });

    return <EvaluationForm type="game" route={gameEvaluation}/>;
}
