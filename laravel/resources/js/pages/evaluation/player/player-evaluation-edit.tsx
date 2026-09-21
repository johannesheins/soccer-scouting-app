import EvaluationForm from "@/components/from/evaluation-form";
import playerEvaluation from "@/routes/evaluation/player";
import evaluation from "@/routes/evaluation";

export default function PlayerEvaluationEdit() {
    return <EvaluationForm type="player" route={playerEvaluation} edit={true}/>;
}

PlayerEvaluationEdit.layout = {
    breadcrumbs: [
        {
            title: 'Bewertung',
            href: evaluation.index(),
        },
        {
            title: 'Interne Spielerbewertung bearbeiten',
        },
    ],
};
