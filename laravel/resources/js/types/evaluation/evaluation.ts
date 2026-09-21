import type {User} from "@/types";
import type {EvaluationCriteriaScore} from "@/types/evaluation-criteria";
import type {Recommendation} from "@/types/recommendation";
import {GameEvaluation, GameEvaluationSmall} from "@/types/evaluation/game-evaluation";
import type {RouteQueryOptions} from "@/wayfinder";
import {PlayerEvaluation, PlayerEvaluationSmall} from "@/types/evaluation/player-evaluation";

export type EvaluationSmall = {
    id: number,
    date: string
    strengths: string,
    weaknesses: string,
    recommendation_id: number,
    comment: string,
    criteria_scores: EvaluationCriteriaScore[],
    created_by: number,
    created_at: string,
    updated_at: string,
}

export type EvaluationSmallTypeMap = {
    game: GameEvaluationSmall,
    player: PlayerEvaluationSmall,
}

export type EvaluationSmallType = keyof EvaluationSmallTypeMap;

export type Evaluation = EvaluationSmall & {
    recommendation: Recommendation,
    creator: User,

    total_score?: number,
    group_scores?: number[]
}

export type EvaluationTypeMap = {
    game: GameEvaluation,
    player: PlayerEvaluation,
}

export type EvaluationRoutes = {
    store: { url: (options?: RouteQueryOptions) => string };
    update: { url: (args: number, options?: RouteQueryOptions) => string };
};
