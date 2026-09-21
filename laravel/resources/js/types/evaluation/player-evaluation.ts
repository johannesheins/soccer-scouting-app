import type {Evaluation, EvaluationSmall} from "@/types/evaluation/evaluation";
import type {PlayerSmall} from "@/types/player";

export type PlayerEvaluationSmall = EvaluationSmall & {
    player_id: number,
}

export type PlayerEvaluation = Evaluation & {
    player: PlayerSmall,
}
