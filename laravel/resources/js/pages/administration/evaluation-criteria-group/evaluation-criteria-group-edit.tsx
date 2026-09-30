import EvaluationCriteriaGroupForm from "@/components/from/evaluation-criteria-group-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";

export default function EvaluationCriteriaGroupEdit() {
    return <EvaluationCriteriaGroupForm edit backHref={evaluationCriteriaGroup.index.url()}/>;
}

EvaluationCriteriaGroupEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('Criteria groups'),
            href: evaluationCriteriaGroup.index(),
        },
        {
            title: t('Edit criteria group'),
        },
    ],
});