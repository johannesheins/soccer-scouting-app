import type {Club} from "@/types/club";
import type {Evaluation, EvaluationSmall} from "@/types/evaluation/evaluation";
import type {PlayerSmall} from "@/types/player";

export type GameEvaluationSmall = EvaluationSmall & {
    player_id: number,
    home_team_id: number,
    away_team_id: number,
}

export type GameEvaluation = Evaluation & {
    player: PlayerSmall,
    home_team: Club,
    away_team: Club,
}
