import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import LanguageTabs from '@/components/language-tabs';
import { t } from '@/locale/translate';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    return (
        <>
            <Head title={t('Appearance')} />

            <h1 className="sr-only">{t('Appearance')}</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title={t('Appearance')}
                    description={t('Update your account\'s appearance settings')}
                />
                <AppearanceTabs />
            </div>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title={t('Language')}
                    description={t('Choose the language used throughout the app')}
                />
                <LanguageTabs />
            </div>
        </>
    );
}

Appearance.layout = () => ({
    breadcrumbs: [
        {
            title: t('Appearance'),
            href: editAppearance(),
        },
    ],
});
