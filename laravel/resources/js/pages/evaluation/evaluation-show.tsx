import {Dialog, DialogContent} from "@/components/ui/dialog";
import {useModal} from "@/lib/inertia-modal";
import EvaluationView from "@/pages/evaluation/evaluation-view";
import type {Evaluation} from "@/types/evaluation/evaluation";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";

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
