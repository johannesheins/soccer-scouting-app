import { Head } from '@inertiajs/react';
import ExpiredPasswordController from '@/actions/App/Http/Controllers/Settings/ExpiredPasswordController';
import PasswordUpdateForm from '@/components/from/password-update-form';
import TextLink from '@/components/text-link';
import { logout } from '@/routes';

export default function PasswordExpired() {
    return (
        <>
            <Head title="Passwort abgelaufen" />

            <div className="space-y-6">
                <PasswordUpdateForm
                    action={ExpiredPasswordController.update()}
                />

                <TextLink
                    href={logout()}
                    className="mx-auto block text-center text-sm"
                >
                    Abmelden
                </TextLink>
            </div>
        </>
    );
}

PasswordExpired.layout = {
    title: 'Passwort abgelaufen',
    description: 'Dein Passwort ist abgelaufen. Bitte lege ein neues Passwort fest, um fortzufahren',
};
