import {Head, Link, router, useForm, usePage} from '@inertiajs/react';
import React, {useEffect} from 'react';
import {
    Field, FieldContent,
    FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import {Right, RightGroup, UserGroup} from "@/types/types";
import userGroup from "@/routes/administration/user-group";
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from "@/components/ui/accordion"
import {Checkbox} from "@/components/ui/checkbox";

const userGroupRoute = userGroup;

type Props = { rightGroups: RightGroup[]; userGroup: UserGroup; };
export default function PlayerForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string|null }) {
    const { rightGroups, userGroup } = usePage<Props>().props;

    const { data, setData, post, put, processing, errors } = useForm({
        name: userGroup?.name ?? '',
        rights: userGroup?.rights.map(r => r.id) ?? []
    });


    function select(right: Right){
        if(data.rights.includes(right.id)){
            setData('rights', data.rights.filter(id => id !== right.id));
        }else{
            setData('rights', [...data.rights, right.id]);
        }
    }

    function selectAll(rights: Right[], isChecked: boolean) {
        if(isChecked){
            const newIds = rights.map(r => r.id).filter(id => !data.rights.includes(id));
            setData('rights', [...data.rights, ...newIds]);
        }else{
            const groupIds = rights.map(r => r.id);
            setData('rights', data.rights.filter(id => !groupIds.includes(id)));
        }
    }

    const items = rightGroups.map(rGroup => ({
        value: String(rGroup.id),
        trigger: rGroup.name,
        content: <FieldSet>
            <Field orientation="horizontal">
                <Checkbox id={"rg"+rGroup.id} onCheckedChange={(checked) => selectAll(rGroup.rights, checked === true)}/>
                <FieldContent>
                    <FieldLabel htmlFor={"rg"+rGroup.id} className={"text-foreground italic"}>
                        Alle ab-/anwählen
                    </FieldLabel>
                </FieldContent>
            </Field>
            {rGroup.rights.map(right => (
                <Field orientation="horizontal" key={right.id} className={"flex items-center"}>
                    <Checkbox id={"r"+right.id} onClick={() => select(right)} checked={data.rights.includes(right.id)}/>
                    <FieldContent>
                        <FieldLabel htmlFor={"r"+right.id}>
                            {right.name}
                        </FieldLabel>
                        <FieldDescription>
                            {right.description}
                        </FieldDescription>
                    </FieldContent>
                </Field>
            ))}
        </FieldSet>
    }));

    function submit(e: React.FormEvent){
        e.preventDefault()
        if(edit && userGroup?.id){
            return put(userGroupRoute.update.url(userGroup.id));
        }
        return post(userGroupRoute.store.url());
    }

    return (
        <>
            <div className="max-w-6xl">
                <form onSubmit={submit}>
                    <Head title={"Benutzergruppe " + (edit ? 'bearbeiten' : 'erstellen')} />
                    <FieldSet>
                        <FieldGroup>
                            <Field>
                                <FieldLabel htmlFor="name">Gruppenname</FieldLabel>
                                <Input id="name"
                                       value={data.name}
                                       onChange={e => setData('name', e.target.value)}
                                />
                                <InputError message={errors.name} />
                            </Field>
                        </FieldGroup>
                        <FieldGroup>
                            <Accordion type="multiple">
                                {items.map((item) => (
                                    <AccordionItem key={item.value} value={item.value}>
                                        <AccordionTrigger>{item.trigger}</AccordionTrigger>
                                        <AccordionContent className="flex flex-col gap-4">{item.content}</AccordionContent>
                                    </AccordionItem>
                                ))}
                            </Accordion>
                        </FieldGroup>
                        <Field className="w-fit flex-row">
                            <Button type="submit" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                            {edit && backHref && <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>Zurück</Button>}
                        </Field>
                    </FieldSet>
                </form>
            </div>
        </>
    );
}
