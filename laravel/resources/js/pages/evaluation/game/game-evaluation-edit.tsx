import EvaluationForm from "@/components/from/evaluation-form";
import gameEvaluation from "@/routes/evaluation/game";
import evaluation from "@/routes/evaluation";

export default function GameEvaluationEdit() {
    return <EvaluationForm type="game" route={gameEvaluation} edit={true}/>;
}

GameEvaluationEdit.layout = {
    breadcrumbs: [
        {
            title: 'Bewertung',
            href: evaluation.index(),
        },
        {
            title: 'Spielbewertung bearbeiten',
        },
    ],
};
