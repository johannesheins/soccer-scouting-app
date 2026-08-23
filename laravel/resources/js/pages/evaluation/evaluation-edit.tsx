import evaluation from "@/routes/evaluation";
import EvaluationForm from "@/components/from/evaluation-form";

export default function EvaluationCreate() {
    return <EvaluationForm edit={true}/>;
}

EvaluationCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler-Bewertungen',
            href: evaluation.index(),
        },
        {
            title: 'Spieler-Bewertungen erstellen',
        },
    ],
};
