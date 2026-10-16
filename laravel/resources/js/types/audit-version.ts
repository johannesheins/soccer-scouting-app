import type {User} from "@/types/auth";

export type AuditVersion = {
    id: number,
    event: 'created' | 'updated' | 'deleted',
    created_at: string,
    user: User | null,
    changes: string[],
}
