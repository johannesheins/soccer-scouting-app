import evaluation from "@/routes/evaluation";
import EvaluationForm from "@/components/from/evaluation-form";
import gameEvaluation from "@/routes/evaluation/game";

export default function GameEvaluationEdit() {
    console.log('game evaluation edit');
    return <EvaluationForm type="game" route={gameEvaluation} edit={true}/>;
}

GameEvaluationEdit.layout = {
    breadcrumbs: [
        {
            title: 'Spielbewertung',
            href: evaluation.index(),
        },
        {
            title: 'Spielbewertung bearbeiten',
        },
    ],
};
