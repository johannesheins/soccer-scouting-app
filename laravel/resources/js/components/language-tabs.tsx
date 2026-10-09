import { usePage } from '@inertiajs/react';
import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';
import { switchLocale } from '@/locale/switch-locale';
import { currentLocale } from '@/locale/translate';

export default function LanguageTabs({
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement>) {
    const { locales } = usePage().props;
    const active = currentLocale();

    return (
        <div
            className={cn(
                'inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800',
                className,
            )}
            {...props}
        >
            {Object.entries(locales).map(([value, label]) => (
                <button
                    key={value}
                    type="button"
                    lang={value.replace('_', '-')}
                    aria-pressed={active === value}
                    onClick={() => value !== active && switchLocale(value)}
                    className={cn(
                        'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                        active === value
                            ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                            : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
                    )}
                >
                    <span className="text-sm">{label}</span>
                </button>
            ))}
        </div>
    );
}
