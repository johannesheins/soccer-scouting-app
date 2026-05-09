import {Head, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useState } from 'react';
import {toPositionOptions, toPlayerPositionIds, groupClubsByLetter, getYearOptions} from '@/hooks/form-options';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import MultipleSelector from "@/components/ui/multi-select";
import {Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue} from "@/components/ui/select";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import type { Club, PlayerSmall, Position } from "@/types/types";

type Props = { positions: Position[]; clubs: Club[]; player?: PlayerSmall };

export default function PlayerForm(edit = false) {
    const { player, positions, clubs } = usePage<Props>().props;

    const yearOfBirthOptions = getYearOptions();
    const positionOptions = toPositionOptions(positions);
    const playerPositions = toPlayerPositionIds(player);
    const clubsByLetter = groupClubsByLetter(clubs);
    const [selectedPositions, setSelectedPositions] = useState(
        positionOptions.filter(o => playerPositions.includes(o.value))
    );

    const { data, setData, post, put, processing, errors } = useForm({
        firstname: player?.firstname ?? '',
        lastname: player?.lastname ?? '',
        year_of_birth: String(player?.year_of_birth) ?? '',
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
                            <Select value={data.year_of_birth} onValueChange={v => setData('year_of_birth', v)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Jahrgang wählen" />
                                </SelectTrigger>
                                <SelectContent>
                                    {yearOfBirthOptions.map(c => (
                                        <SelectItem key={c.value} value={String(c.value)}>{c.label}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.year_of_birth} />
                        </Field>
                    </FieldGroup>
                    <FieldGroup className="grid sm:grid-cols-[2fr_2fr_1fr]">
                        <Field>
                            <FieldLabel htmlFor="club_id">Club</FieldLabel>
                            <Select value={data.club_id} onValueChange={v => setData('club_id', v)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Verein wählen" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.entries(clubsByLetter).sort().map(([letter, group]) => (
                                        <SelectGroup key={letter}>
                                            <SelectLabel>{letter}</SelectLabel>
                                            {group.map(c => (
                                                <SelectItem key={c.id} value={String(c.id)}>{c.clubname}</SelectItem>
                                            ))}
                                        </SelectGroup>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.club_id} />
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
                    <Field className="w-fit">
                        <Button type="submit" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                    </Field>
                </FieldSet>
            </form>
        </>
    );
}
