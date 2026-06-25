import {administration} from '@/routes';
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";
import EvaluationCriteriaGroupForm from "@/components/from/evaluation-criteria-group-form";

export default function EvaluationCriteriaGroupCreate() {
    return <EvaluationCriteriaGroupForm/>;
}

EvaluationCriteriaGroupCreate.layout = {
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
            title: 'Kriteriengruppe erstellen',
        },
    ],
};