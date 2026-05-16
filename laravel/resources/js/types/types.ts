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
    club_id: number,
    positions: { id: number }[],
};

export type Player = {
    id: number,
    firstname: string,
    lastname: string,
    year_of_birth: number,
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

export type EvaluationCriteria = {
    id: number,
    name: string,
    minimum_player_age: number,
    multiplier: number,
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
