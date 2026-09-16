export type RightSmall = {
    id: number
}

export type Right = {
    id: number,
    name: string,
    description: string,
}

export type RightGroup = {
    id: number,
    name: string,
    rights: Right[]
}
