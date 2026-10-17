import EvaluationForm from "@/components/from/evaluation-form";
import { t } from '@/locale/translate';
import playerEvaluation from "@/routes/evaluation/player";
import evaluation from "@/routes/evaluation";

export default function PlayerEvaluationCreate() {
    return <EvaluationForm type="player" route={playerEvaluation}/>;
}

PlayerEvaluationCreate.layout = () => ({
    breadcrumbs: [
        {
            title: t('Evaluations'),
            href: evaluation.search(),
        },
        {
            title: t('Create internal player evaluation'),
        },
    ],
});
