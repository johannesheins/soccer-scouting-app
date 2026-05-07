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
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import {Button} from "@/components/ui/button";

type Position = { id: number; position_code: string };
type Club = { id: number; clubname: string };
type Props = { positions: Position[]; clubs: Club[] };

export default function PlayerCreate() {
    const { positions, clubs } = usePage<Props>().props;
    const [selectedPositions, setSelectedPositions] = useState<Option[]>([]);

    const positionOptions: Option[] = positions.map(p => ({
        value: String(p.id),
        label: p.position_code,
    }));

    const clubOptions = clubs.map(c => ({
        value: String(c.id),
        label: c.clubname,
    }));

    const { data, setData, post, processing } = useForm({
        firstname: '',
        lastname: '',
        age: '',
        club_id: '',
        positions: [] as string[],
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
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="lastname">Nachname</FieldLabel>
                            <Input id="lastname" type="text"
                                value={data.lastname}
                                onChange={e => setData('lastname', e.target.value)}
                            />
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="age">Alter</FieldLabel>
                            <Input id="age" type="number" min="1" className="max-w-30"
                                value={data.age}
                                onChange={e => setData('age', e.target.value)}
                            />
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
                                    {clubOptions.map(c => (
                                        <SelectItem key={c.value} value={c.value}>{c.label}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </Field>
                        <Field>
                            <FieldLabel htmlFor="position_ids">Position</FieldLabel>
                            <MultipleSelector
                                value={selectedPositions}
                                onChange={opts => {
                                    setSelectedPositions(opts);
                                    setData('positions', opts.map(o => o.value));
                                }}
                                defaultOptions={positionOptions}
                                placeholder="Position wählen"
                                hidePlaceholderWhenSelected
                                emptyIndicator={<p className="text-center text-sm">Keine Positionen gefunden</p>}
                            />
                        </Field>
                    </FieldGroup>
                    <Field>
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
