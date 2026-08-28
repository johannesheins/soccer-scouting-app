import {router, useForm} from "@inertiajs/react";
import React from "react";
import InputError from "@/components/input-error";
import {Button} from "@/components/ui/button";
import {Field, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import {usePreviousUrl} from "@/hooks/use-previous-url";
import {useUrlParam} from "@/hooks/useUrlParam";
import club from "@/routes/club";

export default function ClubSearchForm(){
    usePreviousUrl();

    const { data, setData, get, processing, errors } = useForm({
        clubname: useUrlParam('clubname') ?? '',
        zip_code: useUrlParam('zip_code') ?? '',
        city: useUrlParam('city') ?? '',
    });

    function submit(e: React.FormEvent){
        e.preventDefault()

        return get(club.search.url());
    }

    function resetForm(){
        router.get(window.location.pathname);
    }

    return (
        <div className="w-full">
            <form onSubmit={submit}>
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-2 xl:grid-cols-3">
                        <Field>
                            <FieldLabel htmlFor="clubname">Vereinsname</FieldLabel>
                            <Input id="clubname"
                                   value={data.clubname}
                                   onChange={e => setData('clubname', e.target.value)}
                            />
                            <InputError message={errors.clubname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="zip_code">PLZ</FieldLabel>
                            <Input id="zip_code"
                                   value={data.zip_code}
                                   onChange={e => setData('zip_code', e.target.value)}
                            />
                            <InputError message={errors.zip_code} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="city">Stadt</FieldLabel>
                            <Input id="city"
                                   value={data.city}
                                   onChange={e => setData('city', e.target.value)}
                            />
                            <InputError message={errors.city} />
                        </Field>
                    </FieldGroup>
                    <Field className="w-fit flex flex-row">
                        <Button type="submit" disabled={processing}>Suchen</Button>
                        <Button type="button" variant="secondary" onClick={resetForm}>Zurücksetzen</Button>
                    </Field>
                </FieldSet>
            </form>
        </div>
    );
}
