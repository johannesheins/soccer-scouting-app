import EvaluationCriteriaGroupForm from "@/components/from/evaluation-criteria-group-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";

export default function EvaluationCriteriaGroupCreate() {
    return <EvaluationCriteriaGroupForm/>;
}

EvaluationCriteriaGroupCreate.layout = () => ({
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
            title: t('Create criteria group'),
        },
    ],
});