import { Head } from '@inertiajs/react';
import ExpiredPasswordController from '@/actions/App/Http/Controllers/Settings/ExpiredPasswordController';
import PasswordUpdateForm from '@/components/from/password-update-form';

export default function PasswordExpired() {
    return (
        <>
            <Head title="Passwort abgelaufen" />

            <PasswordUpdateForm action={ExpiredPasswordController.update()} />
        </>
    );
}

PasswordExpired.layout = {
    title: 'Passwort abgelaufen',
    description:
        'Dein Passwort ist abgelaufen. Bitte lege ein neues Passwort fest, um fortzufahren',
};
