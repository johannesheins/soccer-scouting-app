import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import {EvaluationCriteria} from "@/types/types";
import evaluationCriteriaRoute from "@/routes/evaluation-criteria";

type Props = { evaluationCriterion?: EvaluationCriteria };

export default function EvaluationCriteriaForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string | null }) {
    const { evaluationCriterion } = usePage<Props>().props;

    const { data, setData, post, put, processing, errors } = useForm({
        name: evaluationCriterion?.name ?? '',
        minimum_player_age: evaluationCriterion?.minimum_player_age ?? '',
        multiplier: evaluationCriterion?.multiplier ?? 1,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        if (edit && evaluationCriterion?.id) {
            return put(evaluationCriteriaRoute.update.url(evaluationCriterion.id));
        }
        return post(evaluationCriteriaRoute.store.url());
    }

    return (
        <>
            <div className="max-w-6xl">
                <form onSubmit={submit}>
                    <Head title={"Bewertungskriterium " + (edit ? 'bearbeiten' : 'erstellen')} />
                    <FieldSet>
                        <FieldGroup>
                            <Field>
                                <FieldLabel htmlFor="name">Name</FieldLabel>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                />
                                <InputError message={errors.name} />
                            </Field>
                        </FieldGroup>
                        <FieldGroup className="grid sm:grid-cols-[1fr_1fr]">
                            <Field>
                                <FieldLabel htmlFor="minimum_player_age">Mindestalter</FieldLabel>
                                <Input
                                    id="minimum_player_age"
                                    type="number"
                                    min={0}
                                    value={data.minimum_player_age}
                                    onChange={e => setData('minimum_player_age', e.target.value === '' ? '' : Number(e.target.value))}
                                />
                                <InputError message={errors.minimum_player_age} />
                            </Field>
                            <Field>
                                <FieldLabel htmlFor="multiplier">Multiplikator</FieldLabel>
                                <Input
                                    id="multiplier"
                                    type="number"
                                    min={1}
                                    value={data.multiplier}
                                    onChange={e => setData('multiplier', Number(e.target.value))}
                                />
                                <InputError message={errors.multiplier} />
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
        </>
    );
}
