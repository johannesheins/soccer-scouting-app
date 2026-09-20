import EvaluationForm from "@/components/from/evaluation-form";
import gameEvaluation from "@/routes/evaluation/game";

export default function GameEvaluationCreate() {
    return <EvaluationForm type="game" route={gameEvaluation}/>;
}

GameEvaluationCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spielbewertungen',
            href: gameEvaluation.index(),
        },
        {
            title: 'Spielbewertungen erstellen',
        },
    ],
};
