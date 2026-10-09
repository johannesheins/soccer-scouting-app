import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import playerEvaluation from "@/routes/evaluation/player";
import evaluation from "@/routes/evaluation";

export default function PlayerEvaluationEdit() {
    return <EvaluationForm type="player" route={playerEvaluation} edit={true}/>;
}

PlayerEvaluationEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Evaluations'),
            href: evaluation.index(),
        },
        {
            title: t('Edit internal player evaluation'),
        },
    ],
});
