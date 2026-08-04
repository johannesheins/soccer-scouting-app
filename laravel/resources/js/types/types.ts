import {User} from "@/types/auth";

export type PositionGroup = {
    id: number,
    name: string,
};

export type Position = {
    id: number,
    position_code: string,
    position_group: PositionGroup | null,
};

export type Club = {
    id: number,
    clubname: string,
};

export type PlayerSmall = {
    id: number,
    firstname: string,
    lastname: string,
    year_of_birth: number,
    height: number,
    strong_foot: string,
    club_id: number,
    positions: { id: number }[],
};

export type PlayerOption = {
    id: number,
    firstname: string,
    lastname: string,
    club: Club,
}

export type Player = {
    id: number,
    firstname: string,
    lastname: string,
    year_of_birth: number,
    height: number,
    strong_foot: string,
    club: Club,
    positions: Position[],
}

export type RightGroup = {
    id: number,
    name: string,
    rights: Right[]
}

export type RightSmall = {
    id: number
}

export type Right = {
    id: number,
    name: string,
    description: string,
}

export type UserGroupSmall = {
    id: number,
}

export type UserGroup = {
    id: number,
    name: string,
    number_of_users: number,
    rights: RightSmall[]
}

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

export type Recommendation = {
    id: number,
    name: string,
}

export type EvaluationSmall = {
    id: number,
    player_id: number,
    home_team_id: number,
    away_team_id: number,
    kickoff_date: string
    kickoff_time: string
    strengths: string,
    weaknesses: string,
    recommendation_id: number,
    comment: string,
    criteria_scores: EvaluationCriteriaScore[],
    created_by: number
}

export type Evaluation = {
    id: number,
    player: PlayerSmall,
    home_team: Club,
    away_team: Club,
    kickoff_date: string
    kickoff_time: string
    strengths: string,
    weaknesses: string,
    recommendation: Recommendation,
    comment: string,
    criteria_scores: EvaluationCriteriaScore[]
    creator: User,
    created_at: string,
    updated_at: string
}

export type EvaluationSearchQuery = {
    criteria_scores_from: number[]
    criteria_scores_to: number[]

    player_ids: number[],
    club_ids: number[],
    years_of_birth: number[],

    open_tab: string,
    open_accordion: boolean[]
}
