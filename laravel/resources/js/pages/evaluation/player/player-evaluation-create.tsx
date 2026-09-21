import EvaluationForm from "@/components/from/evaluation-form";
import playerEvaluation from "@/routes/evaluation/player";
import evaluation from "@/routes/evaluation";

export default function PlayerEvaluationCreate() {
    return <EvaluationForm type="player" route={playerEvaluation}/>;
}

PlayerEvaluationCreate.layout = {
    breadcrumbs: [
        {
            title: 'Bewertung',
            href: evaluation.index(),
        },
        {
            title: 'Interne Spielerbewertung erstellen',
        },
    ],
};
