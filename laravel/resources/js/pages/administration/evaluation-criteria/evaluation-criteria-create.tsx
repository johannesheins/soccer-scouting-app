import {administration} from '@/routes';
import evaluationCriteria from "@/routes/evaluation-criteria";
import EvaluationCriteriaForm from "@/components/from/evaluation-criteria-form";

export default function EvaluationCriteriaCreate() {
    return <EvaluationCriteriaForm />;
}

EvaluationCriteriaCreate.layout = {
    breadcrumbs: [
        {
            title: 'Verwaltung',
            href: administration(),
        },
        {
            title: 'Bewertungskriterien',
            href: evaluationCriteria.index(),
        },
        {
            title: 'Bewertungskriterium erstellen',
        },
    ],
};