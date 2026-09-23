import type {Club} from "@/types/club";
import type {Evaluation, EvaluationSmall} from "@/types/evaluation/evaluation";
import type {PlayerSmall} from "@/types/player";

export type GameEvaluationSmall = EvaluationSmall & {
    player_id: number,
    home_club_id: number,
    home_team: string,
    guest_club_id: number,
    guest_team: string,
}

export type GameEvaluation = Evaluation & {
    player: PlayerSmall,
    home_club: Club,
    home_team: string,
    guest_club: Club,
    guest_team: string,
}
