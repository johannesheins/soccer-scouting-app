import {Head, useForm, usePage} from '@inertiajs/react';
import React, { useState } from 'react';
import { player } from '@/routes';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import MultipleSelector, { type Option } from "@/components/ui/multi-select";
import {Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue} from "@/components/ui/select";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";

type PositionGroup = { id: number; name: string };
type Position = { id: number; position_code: string; position_group: PositionGroup | null };
type Club = { id: number; clubname: string };
type Props = { positions: Position[]; clubs: Club[] };

export default function PlayerCreate() {
    const { positions, clubs } = usePage<Props>().props;
    const [selectedPositions, setSelectedPositions] = useState<Option[]>([]);

    const positionOptions: Option[] = positions.map(p => ({
        value: String(p.id),
        label: p.position_code,
        group: p.position_group?.name ?? '',
    }));

    const clubsByLetter = clubs.reduce<Record<string, Club[]>>((acc, c) => {
        const letter = c.clubname.charAt(0).toUpperCase();
        (acc[letter] ??= []).push(c);
        return acc;
    }, {});

    const { data, setData, post, processing, errors } = useForm({
        firstname: '',
        lastname: '',
        age: '',
        club_id: '',
        position_ids: [] as string[],
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        post('/player');
    }

    return (
        <>
            <form onSubmit={submit}>
                <Head title="Spieler erstellen" />
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
                                    <SelectValue placeholder="Club wählen" />
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
                        <Field className="col-span-2">
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
                        <Button type="submit" disabled={processing}>Erstellen</Button>
                    </Field>
                </FieldSet>
            </form>
        </>
    );
}

PlayerCreate.layout = {
    breadcrumbs: [
        {
            title: 'Spieler',
            href: player(),
        },
        {
            title: 'Spieler erstellen',
        },
    ],
};
