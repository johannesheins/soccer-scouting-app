import { Form, Head, Link } from '@inertiajs/react';
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
            <Head title="Profileinstellungen" />

            <h1 className="sr-only">Profileinstellungen</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Profilinformationen"
                    description="Aktualisiere deinen Namen und deine E-Mail-Adresse"
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
                                <Label htmlFor="firstname">Vorname</Label>

                                <Input
                                    id="firstname"
                                    className="mt-1 block w-full"
                                    defaultValue={user.firstname}
                                    name="firstname"
                                    required
                                    autoComplete="given-name"
                                    placeholder="Vorname"
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.firstname}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="lastname">Nachname</Label>

                                <Input
                                    id="lastname"
                                    className="mt-1 block w-full"
                                    defaultValue={user.lastname}
                                    name="lastname"
                                    required
                                    autoComplete="family-name"
                                    placeholder="Nachname"
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.lastname}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">E-Mail-Adresse</Label>

                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    defaultValue={user.email}
                                    name="email"
                                    required
                                    autoComplete="username"
                                    placeholder="E-Mail-Adresse"
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
                                            Deine E-Mail-Adresse ist nicht verifiziert.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                            >
                                                Klicke hier, um die Bestätigungs-E-Mail
                                                erneut zu senden.
                                            </Link>
                                        </p>

                                        {status ===
                                            'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-green-600">
                                                Ein neuer Bestätigungslink wurde
                                                an deine E-Mail-Adresse gesendet.
                                            </div>
                                        )}
                                    </div>
                                )}

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                >
                                    Speichern
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

Profile.layout = {
    breadcrumbs: [
        {
            title: 'Profileinstellungen',
            href: edit(),
        },
    ],
};
