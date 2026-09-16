export type EvaluationSearchQuery = {
    criteria_scores_from: number[]
    criteria_scores_to: number[]

    player_ids: number[],
    club_ids: number[],
    years_of_birth: number[],

    open_tab: string,
    open_accordion: boolean[]
}
