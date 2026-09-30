import type { VisitOptions } from '@inertiajs/core';
import { router } from '@inertiajs/react';
import { update as updateLocale } from '@/routes/locale';

export function switchLocale(locale: string, options: VisitOptions = {}): void {
    router.patch(
        updateLocale().url,
        { locale },
        { ...options, onSuccess: () => window.location.reload() },
    );
}
