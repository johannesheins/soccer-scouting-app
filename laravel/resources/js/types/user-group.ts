import type {AuditVersion} from "@/types/audit-version";
import type {RightSmall} from "@/types/right";

export type UserGroupSmall = {
    id: number,
}

export type UserGroup = {
    id: number,
    name: string,
    number_of_users: number,
    rights: RightSmall[]
}

export type UserGroupSnapshot = {
    name: string,
    rights: number[],
}

export type UserGroupVersion = AuditVersion & {
    new_values: UserGroupSnapshot,
    userGroup: Omit<UserGroup, 'number_of_users'>,
}
