import type {Club} from "@/types/club";
import type {Position} from "@/types/position";

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

export type playerQuickSearchUserClubs = Club[]

export type PlayerQuickSearchUserYears = {
    year_of_birth: number
}[]
