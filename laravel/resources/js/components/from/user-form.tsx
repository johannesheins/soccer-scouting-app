import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import InputError from "@/components/input-error";
import {Button} from "@/components/ui/button";
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import MultipleSelector from "@/components/ui/multi-select";
import { Separator } from "@/components/ui/separator"
import {toUserGroupOptions} from '@/hooks/form-options';
import { t } from '@/locale/translate';
import user from "@/routes/administration/user";
import type {User} from "@/types";
import type {UserGroup} from "@/types/user-group";

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
                <Head title={edit ? t('Edit user') : t('Create user')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-[1fr_1fr_1fr]">
                        <Field>
                            <FieldLabel htmlFor="firstname">{t('First name')}</FieldLabel>
                            <Input id="firstname"
                                   value={data.firstname}
                                   onChange={e => setData('firstname', e.target.value)}
                            />
                            <InputError message={errors.firstname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="lastname">{t('Last name')}</FieldLabel>
                            <Input id="lastname" type="text"
                                   value={data.lastname}
                                   onChange={e => setData('lastname', e.target.value)}
                            />
                            <InputError message={errors.lastname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="email">{t('Email')}</FieldLabel>
                            <Input id="email" type="text"
                                   value={data.email}
                                   onChange={e => setData('email', e.target.value)}
                            />
                            <InputError message={errors.email} />
                        </Field>
                    </FieldGroup>

                    {!edit && <FieldGroup className="grid sm:grid-cols-[2fr_2fr]">
                        <Field>
                            <FieldLabel htmlFor="password">{t('Password')}</FieldLabel>
                            <Input id="password" type="password"
                                   value={data.password}
                                   onChange={e => setData('password', e.target.value)}
                            />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="password_confirmation">{t('Confirm password')}</FieldLabel>
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
                            <FieldLabel htmlFor="user_groups">{t('User group')}</FieldLabel>
                            <MultipleSelector
                                value={selectedUserGroups}
                                onChange={opts => {
                                    setSelectedUserGroups(opts);
                                    setData('user_groups', opts.map(o => o.value));
                                }}
                                defaultOptions={userGroupOption}
                                placeholder={t('Select a user group')}
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">{t('No user group found')}</p>}
                            />
                            <InputError message={errors.user_groups} />
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
