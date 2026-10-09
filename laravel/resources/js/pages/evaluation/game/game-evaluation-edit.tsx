import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import gameEvaluation from "@/routes/evaluation/game";
import evaluation from "@/routes/evaluation";

export default function GameEvaluationEdit() {
    return <EvaluationForm type="game" route={gameEvaluation} edit={true}/>;
}

GameEvaluationEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Evaluations'),
            href: evaluation.index(),
        },
        {
            title: t('Edit match evaluation'),
        },
    ],
});
