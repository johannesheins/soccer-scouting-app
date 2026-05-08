import {Head, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import { useFormOption } from '@/hooks/use-form-option';
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
import type { Club, PlayerSmall, Position } from "../types/types";

type Props = { positions: Position[]; clubs: Club[]; player?: PlayerSmall };

export default function PlayerForm(edit = false) {
    const { player, positions, clubs } = usePage<Props>().props;

    const { positionOptions, playerPositions, selectedPositions, setSelectedPositions, clubsByLetter } =
        useFormOption(positions, clubs, player);

    const { data, setData, post, put, processing, errors } = useForm({
        firstname: player?.firstname ?? '',
        lastname: player?.lastname ?? '',
        age: player?.age ?? '',
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
