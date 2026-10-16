import type {AuditVersion} from "@/types/audit-version";

export type Club = {
    id: number,
    clubname: string,
    zip_code?: string,
    city?: string,
};

export type ClubVersion = AuditVersion & {
    new_values: Omit<Club, 'id'>,
    club: Club,
}
