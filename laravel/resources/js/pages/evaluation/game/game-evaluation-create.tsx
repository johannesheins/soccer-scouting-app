import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import gameEvaluation from "@/routes/evaluation/game";
import evaluation from "@/routes/evaluation";

export default function GameEvaluationCreate() {
    return <EvaluationForm type="game" route={gameEvaluation}/>;
}

GameEvaluationCreate.layout = () => ({
    breadcrumbs: [
        {
            title: t('Evaluations'),
            href: evaluation.index(),
        },
        {
            title: t('Create match evaluation'),
        },
    ],
});
