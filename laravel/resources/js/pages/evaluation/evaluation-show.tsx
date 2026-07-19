import {Evaluation, EvaluationCriteriaGroups} from "@/types/types";
import {useModal} from "@/lib/inertia-modal";
import {Dialog, DialogContent} from "@/components/ui/dialog";
import EvaluationView from "@/pages/evaluation/evaluation-view";

type Props = {
    evaluation: Evaluation,
    evaluationCriteriaGroups: EvaluationCriteriaGroups[]
}
export default function EvaluationShow({evaluation, evaluationCriteriaGroups}: Props) {
    const { close } = useModal();

    return (
        <Dialog open onOpenChange={(open) => !open && close()}>
            <DialogContent variant="large">
                <EvaluationView evaluation={evaluation} evaluationCriteriaGroups={evaluationCriteriaGroups} />
            </DialogContent>
        </Dialog>
    );
}
