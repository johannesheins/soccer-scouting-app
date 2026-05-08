import {player} from "@/routes";
import {Head, useForm, usePage} from "@inertiajs/react";
import React from "react";
import {Field, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import InputError from "@/components/input-error";

import MultipleSelector from "@/components/ui/multi-select";
import {Button} from "@/components/ui/button";
import { useState } from 'react';
import type { Option } from '@/components/ui/multi-select';
import {toPositionOptions, toClubOptions} from "@/hooks/form-options";
import type {Club, Player, Position} from "@/types/types";

import {PlayerTable} from "@/pages/player/table/player-table";
import {playerColumns} from "@/pages/player/table/player-columns";

type Props = { positions: Position[]; clubs: Club[]; players: Player[]};

export default function PlayerSearch(){
    const { players, positions, clubs } = usePage<Props>().props;

    const positionOptions = toPositionOptions(positions);
    const [selectedPositions, setSelectedPositions] = useState<Option[]>([]);

    const clubOptions = toClubOptions(clubs);
    const [selectedClubs, setSelectedClubs] = useState<Option[]>([]);

    const { data, setData, get, processing, errors, reset } = useForm({
        firstname: '',
        lastname: '',
        age: '',
        club_ids: [] as string[],
        position_ids: [] as string[],
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        return get(`${player.url()}/search`);
    }

    function resetForm(){
        reset();
        setSelectedPositions([]);
    }

    return (
        <>
            <Head title="Spieler suchen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-screen overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <form onSubmit={submit}>
                        <FieldSet>
                            <FieldGroup className="grid sm:grid-cols-[3fr_3fr_1fr]">
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
                                    <FieldLabel htmlFor="age">Alter</FieldLabel>
                                    <Input id="age" type="number" min="1" className="max-w-30"
                                           value={data.age}
                                           onChange={e => setData('age', e.target.value)}
                                    />
                                    <InputError message={errors.age} />
                                </Field>
                            </FieldGroup>
                            <FieldGroup className="grid sm:grid-cols-[3fr_3fr_1fr]">
                                <Field>
                                    <FieldLabel htmlFor="club_id">Club</FieldLabel>
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
                                <Field className="sm:col-span-2">
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
                                <Button type="reset" variant="secondary" onClick={resetForm}>Zurücksetzen</Button>
                            </Field>
                        </FieldSet>
                    </form>
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlayerTable columns={playerColumns} data={players} />
                </div>
            </div>
        </>
    )
}

PlayerSearch.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
        {
            title: 'Spieler suchen',
        },
    ],
};
