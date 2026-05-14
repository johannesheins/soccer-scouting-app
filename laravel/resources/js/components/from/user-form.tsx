import {Head, Link, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import {
    toUserGroupOptions
} from '@/hooks/form-options';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import type {UserGroup, UserGroupSmall} from "@/types/types";
import MultipleSelector from "@/components/ui/multi-select";
import {User} from "@/types";
import user from "@/routes/administration/user";
import { Separator } from "@/components/ui/separator"

type Props = { user: User, userGroups: UserGroup[]; };

const userRoute = user;
export default function UserForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    const { user, userGroups } = usePage<Props>().props;

    const userGroupOption = toUserGroupOptions(userGroups);

    const userGroupIds = user?.user_groups?.map(ug => String(ug.id)) ?? [];

    const [selectedUserGroups, setSelectedUserGroups] = useState(
        userGroupOption.filter(o => userGroupIds.includes(o.value))
    );

    const { data, setData, post, put, processing, errors } = useForm({
        firstname: user?.firstname ?? '',
        lastname: user?.lastname ?? '',
        email: user?.email ?? '',
        password: '',
        password_confirmation: '',
        user_groups: userGroupIds,
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        if(edit && user?.id){
            return put(userRoute.update.url(user.id));
        }
        return post(userRoute.store.url());
    }

    return (
        <>
            <form onSubmit={submit}>
                <Head title={"Spieler " + (edit ? 'bearbeiten' : 'erstellen')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-[1fr_1fr_1fr]">
                        <Field>
                            <FieldLabel htmlFor="firstname">Vorname</FieldLabel>
                            <Input id="firstname"
                                   value={data.firstname}
                                   onChange={e => setData('firstname', e.target.value)}
                            />
                            <InputError message={errors.firstname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="lastname">Nachname</FieldLabel>
                            <Input id="lastname" type="text"
                                   value={data.lastname}
                                   onChange={e => setData('lastname', e.target.value)}
                            />
                            <InputError message={errors.lastname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="email">E-Mail</FieldLabel>
                            <Input id="email" type="text"
                                   value={data.email}
                                   onChange={e => setData('email', e.target.value)}
                            />
                            <InputError message={errors.email} />
                        </Field>
                    </FieldGroup>

                    {!edit && <FieldGroup className="grid sm:grid-cols-[2fr_2fr]">
                        <Field>
                            <FieldLabel htmlFor="password">Password</FieldLabel>
                            <Input id="password" type="password"
                                   value={data.password}
                                   onChange={e => setData('password', e.target.value)}
                            />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="password_confirmation">Password wiederholen</FieldLabel>
                            <Input id="password_confirmation" type="password"
                                   value={data.password_confirmation}
                                   onChange={e => setData('password_confirmation', e.target.value)}
                            />
                            <InputError message={errors.password} />
                        </Field>
                    </FieldGroup>}

                    <Separator className="my-4"/>

                    <FieldGroup className="grid sm:grid-cols-[2fr_2fr]">
                        <Field>
                            <FieldLabel htmlFor="user_groups">Benutzergruppe</FieldLabel>
                            <MultipleSelector
                                value={selectedUserGroups}
                                onChange={opts => {
                                    setSelectedUserGroups(opts);
                                    setData('user_groups', opts.map(o => o.value));
                                }}
                                defaultOptions={userGroupOption}
                                placeholder="Benutzergruppe wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keine Benutzergruppe gefunden</p>}
                            />
                            <InputError message={errors.user_groups} />
                        </Field>
                    </FieldGroup>
                    <Field className="w-fit flex-row">
                        <Button type="submit" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                        {edit && backHref && <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>Zurück</Button>}
                    </Field>
                </FieldSet>
            </form>
        </>
    );
}
