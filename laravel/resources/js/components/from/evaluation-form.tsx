import {Head, router, useForm, usePage} from '@inertiajs/react';
import React from 'react';
import {
    Field, FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldSet,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import {Evaluation, EvaluationCriteria} from "@/types/types";
import evaluationRoute from "@/routes/evaluation";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import ScoreBar from "../score-bar";

type Props = { evaluation?: Evaluation, evaluationCriteria: EvaluationCriteria[] };

export default function EvaluationForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string | null }){
    const { evaluation, evaluationCriteria } = usePage<Props>().props;

    const { data, setData, transform, post, put, processing, errors } = useForm({
        player_id: evaluation?.player_id ?? '',
        home_team_id: evaluation?.home_team_id ?? '',
        away_team_id: evaluation?.away_team_id ?? '',
        criteriaScores: Object.fromEntries(
            (evaluation?.criteria_scores ?? []).map(s => [s.evaluation_criteria_id, s.score])
        ) as Record<number, number>,
    });

    transform(d => ({
        ...d,
        criteriaScores: evaluationCriteria.map(c => ({
            evaluation_criteria_id: c.id,
            score: d.criteriaScores[c.id] ?? 0,
        })),
    }));

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
                    <Tabs defaultValue="player">
                        <TabsList variant="line">
                            <TabsTrigger value="player">Spieler wählen</TabsTrigger>
                            <TabsTrigger value="game">Spieldaten</TabsTrigger>
                            <TabsTrigger value="evaluation">Bewertung</TabsTrigger>
                        </TabsList>
                        <TabsContent value="player">

                        </TabsContent>
                        <TabsContent value="game">

                        </TabsContent>
                        <TabsContent value="evaluation">
                            <FieldSet>
                                <div className="grid grid-cols-2 gap-x-15">
                                    {evaluationCriteria.map((criteria, criteriaId) =>
                                        <FieldGroup key={criteria.id}>
                                            <div className="flex flex-row justify-between">
                                                <FieldLabel htmlFor={'criteria_'+criteria.id}>{criteria.name}</FieldLabel>
                                                <FieldDescription>x{criteria.multiplier}</FieldDescription>
                                            </div>
                                            <ScoreBar name={'criteria_'+criteria.id} value={data.criteriaScores[criteria.id] ?? 0} onChange={val => setData('criteriaScores', { ...data.criteriaScores, [criteria.id]: val })} />
                                            <InputError message={(errors as Record<string, string>)[`criteriaScores.${criteriaId}.score`] ?? ''}/>
                                        </FieldGroup>
                                    )}
                                </div>
                            </FieldSet>
                        </TabsContent>
                    </Tabs>
                    <FieldSet>
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
