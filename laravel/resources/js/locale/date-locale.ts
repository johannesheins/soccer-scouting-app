import type { Locale } from 'date-fns';
import { format as f } from 'date-fns';
import { dateFnsLocale } from '@/locale/translate';

export function date(
    date: string,
    format: string = 'P',
    locale: Locale = dateFnsLocale(),
): string {
    return f(new Date(date), format, { locale: locale });
}
