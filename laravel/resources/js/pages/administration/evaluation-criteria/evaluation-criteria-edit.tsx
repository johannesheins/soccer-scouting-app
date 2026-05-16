import {administration} from '@/routes';
import evaluationCriteria from "@/routes/evaluation-criteria";
import EvaluationCriteriaForm from "@/components/from/evaluation-criteria-form";

export default function EvaluationCriteriaEdit() {
    return <EvaluationCriteriaForm edit backHref={evaluationCriteria.index.url()} />;
}

EvaluationCriteriaEdit.layout = {
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
            title: 'Bewertungskriterium bearbeiten',
        },
    ],
};