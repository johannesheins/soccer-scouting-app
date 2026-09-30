import EvaluationCriteriaForm from "@/components/from/evaluation-criteria-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import evaluationCriteria from "@/routes/evaluation-criteria";

export default function EvaluationCriteriaEdit() {
    return <EvaluationCriteriaForm edit backHref={evaluationCriteria.index.url()} />;
}

EvaluationCriteriaEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('Evaluation criteria'),
            href: evaluationCriteria.index(),
        },
        {
            title: t('Edit evaluation criterion'),
        },
    ],
});