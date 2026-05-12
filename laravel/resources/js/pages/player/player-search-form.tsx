import player from "@/routes/player";
import {router, useForm, usePage} from "@inertiajs/react";
import React from "react";
import {Field, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import InputError from "@/components/input-error";
import MultipleSelector from "@/components/ui/multi-select";
import {Button} from "@/components/ui/button";
import { useState } from 'react';
import type { Option } from '@/components/ui/multi-select';
import {toPositionOptions, toClubOptions, getYearOptions} from "@/hooks/form-options";
import {usePreviousUrl} from "@/hooks/use-previous-url";
import {Club, Player, Position} from "@/types/types";

type Props = { positions: Position[]; clubs: Club[]; players: Player[]};
export default function PlayerSearchForm(){
    usePreviousUrl();
    const { positions, clubs } = usePage<Props>().props;

    const params = new URLSearchParams(window.location.search);

    function getArrayParam(key: string): string[] {
        const bracketed: string[] = [];
        let i = 0;
        while (params.has(`${key}[${i}]`)) {
            bracketed.push(params.get(`${key}[${i}]`)!);
            i++;
        }
        return bracketed.length > 0 ? bracketed : params.getAll(key);
    }

    const urlPositionIds = getArrayParam('position_ids');
    const urlClubIds = getArrayParam('club_ids');
    const urlYearsOfBirth = getArrayParam('years_of_birth');

    const yearOfBirthOptions = getYearOptions();
    const [selectedYearsOfBirth, setSelectedYearsOfBirth] = useState<Option[]>(
        yearOfBirthOptions.filter(o => urlYearsOfBirth.includes(o.value))
    );

    const positionOptions = toPositionOptions(positions);
    const [selectedPositions, setSelectedPositions] = useState<Option[]>(
        positionOptions.filter(o => urlPositionIds.includes(o.value))
    );

    const clubOptions = toClubOptions(clubs);
    // @ts-ignore
    const [selectedClubs, setSelectedClubs] = useState<Option[]>(
        clubOptions.filter(o => urlClubIds.includes(o.value))
    );

    const { data, setData, get, processing, errors } = useForm({
        firstname: params.get('firstname') ?? '',
        lastname: params.get('lastname') ?? '',
        years_of_birth: urlYearsOfBirth,
        club_ids: urlClubIds,
        position_ids: urlPositionIds,
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        return get(player.search.url());
    }

    function resetForm(){
        const baseUrl = window.location.href.replace(window.location.search, '')
        if(baseUrl.endsWith(player.index.url())){
            return router.get(player.index())
        }
        return router.get(player.search());
    }

    return (
        <div className="w-full">
            <form onSubmit={submit}>
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-[1fr_1fr]">
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
                    </FieldGroup>
                    <FieldGroup className="grid sm:grid-cols-[1fr_1fr_1fr]">
                        <Field>
                            <FieldLabel htmlFor="years_of_birth">Jahrgang</FieldLabel>
                            <MultipleSelector
                                value={selectedYearsOfBirth}
                                onChange={opts => {
                                    setSelectedYearsOfBirth(opts);
                                    setData('years_of_birth', opts.map(o => o.value));
                                }}
                                defaultOptions={yearOfBirthOptions}
                                placeholder="Jahrgang wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keinen Jahrgang gefunden</p>}
                            />
                            <InputError message={errors.years_of_birth} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="club_ids">Club</FieldLabel>
                            <MultipleSelector
                                value={selectedClubs}
                                onChange={opts => {
                                    setSelectedClubs(opts);
                                    setData('club_ids', opts.map(o => o.value));
                                }}
                                defaultOptions={clubOptions}
                                groupBy="group"
                                placeholder="Verein wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                            />
                            <InputError message={errors.club_ids} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="position_ids">Position</FieldLabel>
                            <MultipleSelector
                                value={selectedPositions}
                                onChange={opts => {
                                    setSelectedPositions(opts);
                                    setData('position_ids', opts.map(o => o.value));
                                }}
                                defaultOptions={positionOptions}
                                groupBy="group"
                                placeholder="Position wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keine Positionen gefunden</p>}
                            />
                            <InputError message={errors.position_ids} />
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
