import {player} from "@/routes";
import {Head, useForm, usePage} from "@inertiajs/react";
import React from "react";
import {Field, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import InputError from "@/components/input-error";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from "@/components/ui/select";
import MultipleSelector from "@/components/ui/multi-select";
import {Button} from "@/components/ui/button";
import {useFormOption} from "@/hooks/use-form-option";
import type {Club, Player, Position} from "@/types/types";

type Props = { positions: Position[]; clubs: Club[]; players: Player[]};

export default function PlayerSearch(){
    const { players, positions, clubs } = usePage<Props>().props;

    const { positionOptions, selectedPositions, setSelectedPositions, clubsByLetter } =
        useFormOption(positions, clubs);

    const { data, setData, get, processing, errors } = useForm({
        firstname: '',
        lastname: '',
        age: '', //TODO Add from to
        club_id: '', //TODO Add multiselct
        position_ids: [] as string[],
    });

    function submit(e: React.FormEvent){
        e.preventDefault()
        return get('/player/search');
    }

    return (
        <>
            <Head title="Spieler suchen" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-screen overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
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
                            <Button type="submit" disabled={processing}>Suchen</Button>
                        </Field>
                    </FieldSet>
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                     TODO Implement Data-Table (https://ui.shadcn.com/docs/components/radix/data-table) ??? Search client or server side ???
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
