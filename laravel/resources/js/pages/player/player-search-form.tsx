import player from "@/routes/player";
import {router, useForm} from "@inertiajs/react";
import React from "react";
import {Field, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import InputError from "@/components/input-error";
import MultipleSelector from "@/components/ui/multi-select";
import {Button} from "@/components/ui/button";
import { useState, useEffect } from 'react';
import type { Option } from '@/components/ui/multi-select';
import {toPositionOptions, toClubOptions, getYearOptions, getFootOptions} from "@/hooks/form-options";
import {usePreviousUrl} from "@/hooks/use-previous-url";
import {Club, Player, Position} from "@/types/types";
import fetchPlayerSerchData from "@/hooks/fetchApiData";

type Props = { positions: Position[]; clubs: Club[], returnData?: boolean, onResponse?: (players: Player[]) => void };
export default function PlayerSearchForm({ positions, clubs, returnData, onResponse }: Props){
    usePreviousUrl();

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
    const urlStrongFoots = getArrayParam('strong_foots');

    const yearOfBirthOptions = getYearOptions();
    const [selectedYearsOfBirth, setSelectedYearsOfBirth] = useState<Option[]>(
        yearOfBirthOptions.filter(o => urlYearsOfBirth.includes(o.value))
    );

    const footOptions = getFootOptions();
    const [selectedStrongFoots, setSelectedStrongFoots] = useState<Option[]>(
        footOptions.filter(o => urlStrongFoots.includes(o.value))
    );

    const positionOptions = toPositionOptions(positions);
    const [selectedPositions, setSelectedPositions] = useState<Option[]>(
        positionOptions.filter(o => urlPositionIds.includes(o.value))
    );

    const clubOptions = toClubOptions(clubs);
    const [selectedClubs, setSelectedClubs] = useState<Option[]>(
        clubOptions.filter(o => urlClubIds.includes(o.value))
    );

    const { data, setData, get, processing, errors } = useForm({
        firstname: params.get('firstname') ?? '',
        lastname: params.get('lastname') ?? '',
        years_of_birth: urlYearsOfBirth,
        height_from: params.get('height_from') ?? '',
        height_to: params.get('height_to') ?? '',
        strong_foots: urlStrongFoots,
        club_ids: urlClubIds,
        position_ids: urlPositionIds,
    });

    async function submit(e: React.FormEvent){
        e.preventDefault()
        if(returnData){
            const players = await fetchPlayerSerchData(data);
            onResponse?.(players);
            return;
        }
        return get(player.search.url());
    }

    function resetForm(){
        router.get(window.location.pathname);
    }

    useEffect(() => {
        if (!returnData) return;
        fetchPlayerSerchData(data).then(players => onResponse?.(players));
    }, []);

    return (
        <div className="w-full">
            <form onSubmit={submit}>
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-2 lg:grid-cols-3">
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
                        <FieldGroup className="grid grid-cols-2 gap-2 items-center">
                            <Field>
                                <FieldLabel htmlFor="height_from">Größe von</FieldLabel>
                                <Input id="height_from"
                                       value={data.height_from}
                                       onChange={e => setData('height_from', e.target.value)}
                                       placeholder="Größe eintragen"
                                       type="number"
                                />
                                <InputError message={errors.height_from} />
                            </Field>
                            <Field>
                                <FieldLabel htmlFor="height_to">Größe bis</FieldLabel>
                                <Input id="height_to"
                                       value={data.height_to}
                                       onChange={e => setData('height_to', e.target.value)}
                                       placeholder="Größe eintragen"
                                       type="number"
                                />
                                <InputError message={errors.height_to} />
                            </Field>
                        </FieldGroup>
                        <Field>
                            <FieldLabel htmlFor="strong_foot">Starker Fuß</FieldLabel>
                            <MultipleSelector
                                value={selectedStrongFoots}
                                onChange={opts => {
                                    setSelectedStrongFoots(opts);
                                    setData('strong_foots', opts.map(o => o.value));
                                }}
                                defaultOptions={footOptions}
                                groupBy="group"
                                placeholder="Starken Fuß wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Kein treffer</p>}
                            />
                            <InputError message={errors.strong_foots} />
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
