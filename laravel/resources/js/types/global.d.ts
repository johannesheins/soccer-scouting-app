import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            locale: string;
            locales: Record<string, string>; // locale code => language name
            localeConflict: { account: string; browser: string } | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
