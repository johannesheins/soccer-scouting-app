import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import {
    toPositionOptions,
    toPlayerPositionIds,
    toClubOptions,
    getYearOptions, getFootOptions
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
import type { Club, PlayerSmall, Position } from "@/types/types";
import {SingleSelector} from "@/components/ui/single-select";
import MultipleSelector from "@/components/ui/multi-select";

type Props = { positions: Position[]; clubs: Club[]; player?: PlayerSmall };

export default function PlayerForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    const { player, positions, clubs } = usePage<Props>().props;

    const yearOfBirthOptions = getYearOptions();
    const footOptions = getFootOptions();
    const positionOptions = toPositionOptions(positions);
    const playerPositions = toPlayerPositionIds(player);
    const clubOptions = toClubOptions(clubs);

    const [selectedYearOfBirth, setSelectedYearOfBirth] = useState(
        yearOfBirthOptions.filter(o => o.value === String(player?.year_of_birth))
    );
    const [selectedStrongFoot, setSelectedStrongFoot] = useState(
        footOptions.filter(o => o.value === player?.strong_foot)
    );
    const [selectedClub, setSelectedClub] = useState(
        clubOptions.filter(o => o.value === String(player?.club_id))
    );
    const [selectedPositions, setSelectedPositions] = useState(
        positionOptions.filter(o => playerPositions.includes(o.value))
    );

    const { data, setData, post, put, processing, errors } = useForm({
        firstname: player?.firstname ?? '',
        lastname: player?.lastname ?? '',
        year_of_birth: String(player?.year_of_birth) ?? '',
        strong_foot: player?.strong_foot ?? '',
        club_id: String(player?.club_id) ?? '',
        position_ids: playerPositions ?? [] as string[],
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        if(edit && player?.id){
            return put(`/player/${player.id}`);
        }
        return post('/player');
    }

    return (
        <>
            <form onSubmit={submit}>
                <Head title={"Spieler " + (edit ? 'bearbeiten' : 'erstellen')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-[1fr_1fr]">
                        <Field>
                            <FieldLabel htmlFor="firstname">Vorname</FieldLabel>
                            <Input id="firstname"
                                   value={data.firstname}
                                   onChange={e => setData('firstname', e.target.value)}
                                   placeholder="Vorname eintragen"
                            />
                            <InputError message={errors.firstname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="lastname">Nachname</FieldLabel>
                            <Input id="lastname" type="text"
                                   value={data.lastname}
                                   onChange={e => setData('lastname', e.target.value)}
                                   placeholder="Nachname eintragen"
                            />
                            <InputError message={errors.lastname} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="year_of_birth">Jahrgang</FieldLabel>
                            <SingleSelector
                                value={selectedYearOfBirth}
                                onChange={opts => {
                                    setSelectedYearOfBirth(opts);
                                    setData('year_of_birth', opts[0]?.value ?? '');
                                }}
                                defaultOptions={yearOfBirthOptions}
                                placeholder="Jahrgang wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keinen Jahrgang gefunden</p>}
                            />
                            <InputError message={errors.year_of_birth} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="club_id">Club</FieldLabel>
                            <SingleSelector
                                value={selectedClub}
                                onChange={opts => {
                                    setSelectedClub(opts);
                                    setData('club_id', opts[0]?.value ?? '');
                                }}
                                defaultOptions={clubOptions}
                                groupBy="group"
                                placeholder="Verein wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                            />
                            <InputError message={errors.club_id} />
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
                        <Field>
                            <FieldLabel htmlFor="strong_foot">Starker Fuß</FieldLabel>
                            <SingleSelector
                                value={selectedStrongFoot}
                                onChange={opts => {
                                    setSelectedStrongFoot(opts);
                                    setData('strong_foot', opts[0]?.value ?? '');
                                }}
                                defaultOptions={footOptions}
                                groupBy="group"
                                placeholder="Starken Fuß wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Kein treffer</p>}
                            />
                            <InputError message={errors.strong_foot} />
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
