import {PlayerEvaluation} from "@/types/evaluation/player-evaluation";
import {GameEvaluation} from "@/types/evaluation/game-evaluation";
import {EvaluationTypes} from "@/enums";

export type EvaluationSearchResult = PlayerEvaluation & GameEvaluation & {
    evaluation_type: EvaluationTypes
}
