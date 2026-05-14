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

export type UserGroup = {
    id: number,
    name: string,
    numberOfUsers: number,
    rights: RightSmall[]
}
