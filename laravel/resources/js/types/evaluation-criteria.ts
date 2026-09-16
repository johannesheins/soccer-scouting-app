export type EvaluationCriteriaGroup = {
    id: number,
    name: string,
    evaluation_criteria_count?: number,
}

export type EvaluationCriteria = {
    id: number,
    name: string,
    minimum_player_age: number,
    evaluation_criteria_group_id: number | null,
    group: EvaluationCriteriaGroup | null,
}

export type EvaluationCriteriaGroups = {
    id: number,
    name: string,
    evaluation_criteria: EvaluationCriteria[],
}

export type EvaluationCriteriaScore = {
    evaluation_criteria_id: number,
    score: number;
}
