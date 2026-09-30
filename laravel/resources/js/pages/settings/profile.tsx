import { Form, Head, Link } from '@inertiajs/react';
import { t } from '@/locale/translate';
import { useUser } from '@/hooks/use-auth';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/delete-user';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

export default function Profile({
    mustVerifyEmail,
    status,
}: {
    mustVerifyEmail: boolean;
    status?: string;
}) {
    const user = useUser();

    return (
        <>
            <Head title={t('Profile settings')} />

            <h1 className="sr-only">{t('Profile settings')}</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title={t('Profile information')}
                    description={t('Update your name and email address')}
                />

                <Form
                    action={ProfileController.update().url}
                    method={ProfileController.update().method}
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="firstname">{t('First name')}</Label>

                                <Input
                                    id="firstname"
                                    className="mt-1 block w-full"
                                    defaultValue={user.firstname}
                                    name="firstname"
                                    required
                                    autoComplete="given-name"
                                    placeholder={t('First name')}
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.firstname}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="lastname">{t('Last name')}</Label>

                                <Input
                                    id="lastname"
                                    className="mt-1 block w-full"
                                    defaultValue={user.lastname}
                                    name="lastname"
                                    required
                                    autoComplete="family-name"
                                    placeholder={t('Last name')}
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.lastname}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">{t('Email address')}</Label>

                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    defaultValue={user.email}
                                    name="email"
                                    required
                                    autoComplete="username"
                                    placeholder={t('Email address')}
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.email}
                                />
                            </div>

                            {mustVerifyEmail &&
                                user.email_verified_at === null && (
                                    <div>
                                        <p className="-mt-4 text-sm text-muted-foreground">
                                            {t('Your email address is unverified.')}{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                {t(
                                                    'Click here to resend the verification email.',
                                                )}
                                            </Link>
                                        </p>

                                        {status ===
                                            'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-green-600">
                                                {t(
                                                    'A new verification link has been sent to your email address.',
                                                )}
                                            </div>
                                        )}
                                    </div>
                                )}

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                >
                                    {t('Save')}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>

            <DeleteUser />
        </>
    );
}

Profile.layout = () => ({
    breadcrumbs: [
        {
            title: t('Profile settings'),
            href: edit(),
        },
    ],
});
