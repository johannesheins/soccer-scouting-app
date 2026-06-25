import {administration} from '@/routes';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";
import EvaluationCriteriaGroupForm from "@/components/from/evaluation-criteria-group-form";

export default function EvaluationCriteriaGroupEdit() {
    return <EvaluationCriteriaGroupForm edit backHref={evaluationCriteriaGroup.index.url()}/>;
}

EvaluationCriteriaGroupEdit.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Kriteriengruppen',
            href: evaluationCriteriaGroup.index(),
        },
        {
            title: 'Kriteriengruppe bearbeiten',
        },
    ],
};