import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import InputError from "@/components/input-error";
import {Button} from "@/components/ui/button";
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import {ClubRequestNameEnum as Name} from "@/enums";
import club from "@/routes/club";
import type {Club} from "@/types/types";

const clubRoute = club

type Props = { club?: Club };

export default function ClubForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    const { club } = usePage<Props>().props;

    const { data, setData, post, put, processing, errors } = useForm({
        [Name.clubname]: club?.clubname ?? '',
        [Name.zipCode]: club?.zip_code ?? '',
        [Name.city]: club?.city ?? '',
    });

    async function submit(e: React.FormEvent){
        e.preventDefault()

        if(edit && club?.id){
            return put(clubRoute.update.url(club.id));
        }

        return post(clubRoute.store.url());
    }

    return (
        <>
            <form onSubmit={submit}>
                <Head title={"Verein " + (edit ? 'bearbeiten' : 'erstellen')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-2 lg:grid-cols-3">
                        <Field>
                            <FieldLabel htmlFor={Name.clubname}>Vereinsname</FieldLabel>
                            <Input id={Name.clubname}
                                   value={data[Name.clubname]}
                                   onChange={e => setData(Name.clubname, e.target.value)}
                                   placeholder="Vereinsname eintragen"
                            />
                            <InputError message={errors[Name.clubname]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.zipCode}>PLZ</FieldLabel>
                            <Input id={Name.zipCode}
                                   value={data[Name.zipCode]}
                                   onChange={e => setData(Name.zipCode, e.target.value)}
                                   placeholder="PLZ eintragen"
                            />
                            <InputError message={errors[Name.zipCode]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.city}>Stadt</FieldLabel>
                            <Input id={Name.city}
                                   value={data[Name.city]}
                                   onChange={e => setData(Name.city, e.target.value)}
                                   placeholder="Stadt eintragen"
                            />
                            <InputError message={errors[Name.city]} />
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
