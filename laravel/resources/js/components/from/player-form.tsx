import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import {
    toPositionOptions,
    toPlayerPositionIds,
    getFootOptions
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
import type {Club, Player, PlayerSmall, Position} from "@/types/types";
import {SingleSelector} from "@/components/ui/single-select";
import MultipleSelector from "@/components/ui/multi-select";
import player from "@/routes/player"; //used as playerRoute
import api from "@/routes/api";
import {fetchPlayerData} from "@/hooks/fetchApiData";
import {PlayerRequestNameEnum as Name} from "@/enums";
import ClubInput from "@/components/input/club-input";
import YearOfBirthInput from "@/components/input/year-of-birth-input";
import HeightInput from "@/components/input/height-input";

const playerRoute = player

type Props = { positions: Position[]; clubs: Club[]; player?: PlayerSmall };

export default function PlayerForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    return <Form edit={edit} backHref={backHref}/>;
}

export function PlayerFormDialog({onSelectPlayer}: { onSelectPlayer?: (player: Player) => void }){
    return <Form dialog={true} onResponse={onSelectPlayer}/>
}


function Form({edit = false, dialog = false, backHref, onResponse}: { edit?: boolean, dialog?: boolean, backHref?: string|null, onResponse?: (players: Player) => void }) {const { player, positions, clubs } = usePage<Props>().props;

    const footOptions = getFootOptions();
    const positionOptions = toPositionOptions(positions);
    const playerPositions = toPlayerPositionIds(player);

    const [selectedStrongFoot, setSelectedStrongFoot] = useState(
        footOptions.filter(o => o.value === player?.strong_foot)
    );
    const [selectedPositions, setSelectedPositions] = useState(
        positionOptions.filter(o => playerPositions.includes(o.value))
    );

    const { data, setData, post, put, processing, errors } = useForm({
        [Name.firstname]: player?.firstname ?? '',
        [Name.lastname]: player?.lastname ?? '',
        [Name.yearOfBirth]: String(player?.year_of_birth) ?? '',
        [Name.height]: String(player?.height ?? ''),
        [Name.strongFoot]: player?.strong_foot ?? '',
        [Name.clubId]: String(player?.club_id) ?? '',
        [Name.positionIds]: playerPositions ?? [] as string[],
    });

    async function submit(e: React.FormEvent){
        e.preventDefault()
        if(edit && player?.id){
            return put(`/player/${player.id}`);
        }
        if(dialog){
            const player = await fetchPlayerData(data, api.player.store.url());
            onResponse?.(player);
            return;
        }
        return post(playerRoute.store.url());
    }

    return (
        <>
            <form onSubmit={submit}>
                <Head title={"Spieler " + (edit ? 'bearbeiten' : 'erstellen')} />
                <FieldSet>
                    <FieldGroup className="grid sm:grid-cols-2 lg:grid-cols-3">
                        <Field>
                            <FieldLabel htmlFor={Name.firstname}>Vorname</FieldLabel>
                            <Input id={Name.firstname}
                                   value={data[Name.firstname]}
                                   onChange={e => setData(Name.firstname, e.target.value)}
                                   placeholder="Vorname eintragen"
                            />
                            <InputError message={errors[Name.firstname]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.lastname}>Nachname</FieldLabel>
                            <Input id={Name.lastname} type="text"
                                   value={data[Name.lastname]}
                                   onChange={e => setData(Name.lastname, e.target.value)}
                                   placeholder="Nachname eintragen"
                            />
                            <InputError message={errors[Name.lastname]} />
                        </Field>
                        <Field>
                            <ClubInput variant={"single"} name={Name.clubId} clubs={clubs} setData={setData} selectedValues={[Number(data[Name.clubId])]} />
                        </Field>
                        <Field>
                            <YearOfBirthInput variant="single" name={Name.yearOfBirth} setData={setData} selectedValues={[Number(data[Name.yearOfBirth])]} error={errors[Name.yearOfBirth]} />
                        </Field>
                        <Field>
                            {/*Its always zero --- is fixed must be tested*/}
                            <HeightInput name={Name.height} value={data[Name.height]} setData={setData} error={errors[Name.height]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.strongFoot}>Starker Fuß</FieldLabel>
                            <SingleSelector
                                value={selectedStrongFoot}
                                onChange={opts => {
                                    setSelectedStrongFoot(opts);
                                    setData(Name.strongFoot, opts[0]?.value ?? '');
                                }}
                                defaultOptions={footOptions}
                                groupBy="group"
                                placeholder="Starken Fuß wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Kein treffer</p>}
                            />
                            <InputError message={errors[Name.strongFoot]} />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor={Name.positionIds}>Position</FieldLabel>
                            <MultipleSelector
                                value={selectedPositions}
                                onChange={opts => {
                                    setSelectedPositions(opts);
                                    setData(Name.positionIds, opts.map(o => o.value));
                                }}
                                defaultOptions={positionOptions}
                                groupBy="group"
                                placeholder="Position wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keine Positionen gefunden</p>}
                            />
                            <InputError message={errors[Name.positionIds]} />
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
