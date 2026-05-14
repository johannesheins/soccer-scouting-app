import {Head, Link, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import {
    toPositionOptions,
    toPlayerPositionIds,
    toClubOptions,
    getYearOptions,
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
import type {Club, PlayerSmall, Position, UserGroup} from "@/types/types";
import {SingleSelector} from "@/components/ui/single-select";
import MultipleSelector from "@/components/ui/multi-select";
import {User} from "@/types";
import user from "@/routes/administration/user";

type Props = { user: User, userGroups: UserGroup[]; };

const userRoute = user;
export default function UserForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    const { user, userGroups } = usePage<Props>().props;

    const userGroupOption = toUserGroupOptions(userGroups);

    const [selectedUserGroups, setSelectedUserGroups] = useState(
        userGroupOption.filter(o => o.value === String(user?.userGroups))
    );

    const { data, setData, post, put, processing, errors } = useForm({
        firstname: user.firstname ?? '',
        lastname: user.lastname ?? '',
        password: '',
        userGroups: user.userGroups.map(ug => String(ug.id)) ?? [] as string[]
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
                    <FieldGroup className="grid sm:grid-cols-[2fr_2fr_1fr]">
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
                            <FieldLabel htmlFor="year_of_birth">Jahrgang</FieldLabel>
                            <MultipleSelector
                                value={selectedUserGroups}
                                onChange={opts => {
                                    setSelectedUserGroups(opts);
                                    setData('userGroups', opts.map(o => o.value));
                                }}
                                defaultOptions={userGroupOption}
                                placeholder="Benutzergruppe wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keine Benutzergruppe gefunden</p>}
                            />
                            <InputError message={errors.userGroups} />
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
