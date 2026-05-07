export type Player = {
    id?: number,
    firstname?: string,
    lastname?: string,
    age?: number,
    club_id?: number,
    positions: { id: number }[]
}
