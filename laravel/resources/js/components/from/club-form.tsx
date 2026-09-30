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
import { t } from '@/locale/translate';
import club from "@/routes/club";
import type {Club} from "@/types/club";

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
                <Head title={edit ? t('Edit club') : t('Create club')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-2 lg:grid-cols-3">
                        <Field>
                            <FieldLabel htmlFor={Name.clubname}>{t('Club name')}</FieldLabel>
                            <Input id={Name.clubname}
                                   value={data[Name.clubname]}
                                   onChange={e => setData(Name.clubname, e.target.value)}
                                   placeholder={t('Enter club name')}
                            />
                            <InputError message={errors[Name.clubname]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.zipCode}>{t('Postcode')}</FieldLabel>
                            <Input id={Name.zipCode}
                                   value={data[Name.zipCode]}
                                   onChange={e => setData(Name.zipCode, e.target.value)}
                                   placeholder={t('Enter postcode')}
                            />
                            <InputError message={errors[Name.zipCode]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.city}>{t('City')}</FieldLabel>
                            <Input id={Name.city}
                                   value={data[Name.city]}
                                   onChange={e => setData(Name.city, e.target.value)}
                                   placeholder={t('Enter city')}
                            />
                            <InputError message={errors[Name.city]} />
                        </Field>
                    </FieldGroup>
                    <Field className="w-fit flex-row">
                        <Button type="submit" disabled={processing}>{edit ? t('Update') : t('Create')}</Button>
                        {edit && backHref && <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>{t('Back')}</Button>}
                    </Field>
                </FieldSet>
            </form>
        </>
    );
}
