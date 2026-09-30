import EvaluationCriteriaForm from "@/components/from/evaluation-criteria-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import evaluationCriteria from "@/routes/evaluation-criteria";

export default function EvaluationCriteriaCreate() {
    return <EvaluationCriteriaForm />;
}

EvaluationCriteriaCreate.layout = () => ({
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
            title: t('Create evaluation criterion'),
        },
    ],
});