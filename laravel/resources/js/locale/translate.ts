import type { Locale } from 'date-fns';
import { de, enUS } from 'date-fns/locale';

type Translations = Record<string, string>;
type Replacements = Record<string, string | number>;

const localeFiles = import.meta.glob<Translations>('../../../lang/*.json', {
    eager: true,
    import: 'default',
});

const dateFnsLocales: Record<string, Locale> = { de, en: enUS };

// Set from Inertia's shared `locale` prop; an SSR entry has no document and must call it before rendering.
let activeLocale: string | null = null;

export function setActiveLocale(next: string): void {
    activeLocale = next;
}

// Laravel locale code ("pt_BR"); <html lang> holds the BCP 47 form ("pt-BR").
export function currentLocale(): string {
    const lang = typeof document !== 'undefined' ? document.documentElement.lang : '';

    return activeLocale ?? (lang ? lang.replace('-', '_') : 'en');
}

export function currentLanguageTag(): string {
    return currentLocale().replace('_', '-');
}

const language = (locale: string): string => locale.split(/[-_]/)[0];

function translationsFor(locale: string): Translations {
    return localeFiles[`../../../lang/${locale}.json`] ?? localeFiles[`../../../lang/${language(locale)}.json`] ?? {};
}

export function dateFnsLocale(): Locale {
    return dateFnsLocales[language(currentLocale())] ?? enUS;
}

export function t(key: string, replacements: Replacements = {}): string {
    const translation = translationsFor(currentLocale())[key] ?? key;

    return Object.entries(replacements).reduce(
        (result, [name, value]) => result.replaceAll(`:${name}`, String(value)),
        translation,
    );
}
