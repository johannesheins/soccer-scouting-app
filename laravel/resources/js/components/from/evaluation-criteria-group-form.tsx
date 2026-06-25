import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import {Input} from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import {EvaluationCriteriaGroup} from "@/types/types";
import evaluationCriteriaGroup from "@/routes/evaluation-criteria-group";

type Props = { evaluationCriteriaGroup?: EvaluationCriteriaGroup };

export default function EvaluationCriteriaGroupForm({edit = false, backHref = null}: {
    edit?: boolean,
    backHref?: string | null
}) {
    const {evaluationCriteriaGroup: group} = usePage<Props>().props;

    const {data, setData, post, put, processing, errors} = useForm({
        name: group?.name ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        if (edit && group?.id) {
            return put(evaluationCriteriaGroup.update.url(group.id));
        }
        return post(evaluationCriteriaGroup.store.url());
    }

    return (
        <div className="max-w-6xl">
            <form onSubmit={submit}>
                <Head title={"Kriteriengruppe " + (edit ? 'bearbeiten' : 'erstellen')}/>
                <FieldSet>
                    <FieldGroup>
                        <Field>
                            <FieldLabel htmlFor="name">Name</FieldLabel>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                            />
                            <InputError message={errors.name}/>
                        </Field>
                    </FieldGroup>
                    <Field className="w-fit flex-row">
                        <Button type="submit" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                        {edit && backHref && (
                            <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>
                                Zurück
                            </Button>
                        )}
                    </Field>
                </FieldSet>
            </form>
        </div>
    );
}