import { Head } from '@inertiajs/react';
import ExpiredPasswordController from '@/actions/App/Http/Controllers/Settings/ExpiredPasswordController';
import PasswordUpdateForm from '@/components/from/password-update-form';
import TextLink from '@/components/text-link';
import { t } from '@/locale/translate';
import { logout } from '@/routes';

export default function PasswordExpired() {
    return (
        <>
            <Head title={t('Password expired')} />

            <PasswordUpdateForm action={ExpiredPasswordController.update()}>
                <TextLink href={logout()} className="ml-auto text-sm">
                    {t('Log out')}
                </TextLink>
            </PasswordUpdateForm>
        </>
    );
}

PasswordExpired.layout = () => ({
    title: t('Password expired'),
    description:
        t('Your password has expired. Please set a new password to continue'),
});
