import { usePage } from '@inertiajs/react';
import type { Auth, User } from '@/types/auth';

export function useAuth(): Auth {
    return usePage<{ auth: Auth }>().props.auth;
}

export function useUser(): User {
    return useAuth().user;
}