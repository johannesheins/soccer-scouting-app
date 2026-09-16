import type {User} from "@/types";
import type {EvaluationCriteriaScore} from "@/types/evaluation-criteria";
import type {Recommendation} from "@/types/recommendation";

export type EvaluationSmall = {
    id: number,
    date: string
    strengths: string,
    weaknesses: string,
    recommendation_id: number,
    comment: string,
    criteria_scores: EvaluationCriteriaScore[],
    created_by: number
}

export type Evaluation = EvaluationSmall & {
    recommendation: Recommendation,
    creator: User,

    total_score?: number,
    group_scores?: number[]
}
