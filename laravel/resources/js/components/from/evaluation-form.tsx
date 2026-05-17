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
import {Evaluation} from "@/types/types";
import evaluationRoute from "@/routes/evaluation";

type Props = { evaluation?: Evaluation };

export default function EvaluationForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string | null }){
    const { evaluation, evaluationCriteria } = usePage<Props>().props;

    const { data, setData, post, put, processing, errors } = useForm({
        player_id: evaluation?.player_id ?? '',
        home_team_id: evaluation?.home_team_id ?? '',
        away_team_id: evaluation?.away_team_id ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        if (edit && evaluation?.id) {
            return put(evaluationRoute.update.url(evaluation.id));
        }
        return post(evaluationRoute.store.url());
    }

    return (
        <>
            <div className="max-w-6xl">
                <form onSubmit={submit}>
                    <Head title={"Bewertungskriterium " + (edit ? 'bearbeiten' : 'erstellen')} />
                    <FieldSet>
                        <FieldGroup>

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
