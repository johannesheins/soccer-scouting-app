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

export type Player = {
    id?: number,
    firstname?: string,
    lastname?: string,
    age?: number,
    club_id?: number,
    positions: { id: number }[],
};
