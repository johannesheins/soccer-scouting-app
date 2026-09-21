import EvaluationForm from "@/components/from/evaluation-form";
import gameEvaluation from "@/routes/evaluation/game";
import evaluation from "@/routes/evaluation";

export default function GameEvaluationCreate() {
    return <EvaluationForm type="game" route={gameEvaluation}/>;
}

GameEvaluationCreate.layout = {
    breadcrumbs: [
        {
            title: 'Bewertung',
            href: evaluation.index(),
        },
        {
            title: 'Spielbewertungen erstellen',
        },
    ],
};
