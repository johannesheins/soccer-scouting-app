import {Head, router, useForm, usePage} from '@inertiajs/react';
import React, {useEffect, useState} from 'react';
import {
    Field, FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSet,
} from "@/components/ui/field"
import {Button} from "@/components/ui/button";
import InputError from "@/components/input-error";
import {
    type Club,
    Evaluation,
    EvaluationCriteriaGroups,
    Player,
    type Position, Recommendation
} from "@/types/types";
import evaluationRoute from "@/routes/evaluation";
import ScoreBar from "../score-bar";
import PlayerSearchDialog from "@/pages/player/player-search-dialog";
import {Input} from "@/components/ui/input";
import {SingleSelector} from "@/components/ui/single-select";
import {toClubOptions, toRecommendationOptions} from "@/hooks/form-options";
import {DateTimePicker} from "@/components/ui/date-time-picker";
import {Textarea} from "@/components/ui/textarea";

type Props = {
    evaluation?: Evaluation,
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    positions: Position[];
    clubs: Club[],
    recommendations: Recommendation[],
};

export default function EvaluationForm({ edit = false, backHref = null }: { edit?: boolean, backHref?: string | null }){
    const { evaluation, evaluationCriteriaGroups, positions, clubs, recommendations } = usePage<Props>().props;
    const [selectedPlayer, setSelectedPlayer] = useState<Player>();
    useEffect(() => {
        setData('player_id', String(selectedPlayer?.id));
    }, [selectedPlayer]);

    const clubOptions = toClubOptions(clubs);
    const [selectedHomeTeam, setSelectedHomeTeam] = useState(
        clubOptions.filter(o => o.value === String(evaluation?.home_team_id))
    );
    const [selectedAwayTeam, setSelectedAwayTeam] = useState(
        clubOptions.filter(o => o.value === String(evaluation?.away_team_id))
    );

    const recommendationOptions = toRecommendationOptions(recommendations);
    const [selectedRecommendation, setSelectedRecommendation] = useState(
        recommendationOptions.filter(o => o.value === String(evaluation?.home_team_id))
    );

    const { data, setData, transform, post, put, processing, errors } = useForm({
        player_id: evaluation?.player_id ?? '',
        home_team_id: evaluation?.home_team_id ?? '',
        away_team_id: evaluation?.away_team_id ?? '',
        kickoff_date: evaluation?.kickoff_date ?? '',
        kickoff_time: evaluation?.kickoff_time ?? '',
        strengths: evaluation?.strengths ?? '',
        weaknesses: evaluation?.weaknesses ?? '',
        recommendation_id: evaluation?.recommendation_id ?? '',
        comment: evaluation?.comment,
        criteriaScores: Object.fromEntries(
            (evaluation?.criteria_scores ?? []).map(s => [s.evaluation_criteria_id, s.score])
        ) as Record<number, number>,
    });

    const flatCriteria = evaluationCriteriaGroups.flatMap(g => g.evaluation_criteria);

    transform(d => ({
        ...d,
        criteriaScores: evaluationCriteriaGroups.flatMap(group =>
            group.evaluation_criteria.map(criteria => ({
                evaluation_criteria_id: criteria.id,
                score: d.criteriaScores[criteria.id] ?? 0,
            }))
        ),
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
                <Head title={"Bewertung " + (edit ? 'bearbeiten' : 'erstellen')} />

                <div className="flex flex-1 flex-col gap-4 p-4">
                    <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                        <FieldSet>
                            <FieldLegend>Spieler</FieldLegend>
                            <FieldGroup>
                                <Field>
                                    <PlayerSearchDialog positions={positions} clubs={clubs} selectPlayer={true} onSelectedPlayer={setSelectedPlayer}/>
                                    <InputError message={errors.player_id} />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </div>

                    <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                        <FieldSet>
                            <FieldLegend>Spieldaten</FieldLegend>
                            <FieldGroup className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="home_team_id">Heimverein</FieldLabel>
                                    <SingleSelector
                                        value={selectedHomeTeam}
                                        onChange={opts => {
                                            setSelectedHomeTeam(opts);
                                            setData('home_team_id', opts[0]?.value ?? '');
                                        }}
                                        defaultOptions={clubOptions}
                                        groupBy="group"
                                        placeholder="Heimmverein wählen"
                                        hidePlaceholderWhenSelected
                                        emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                                    />
                                    <InputError message={errors.home_team_id} />
                                </Field>
                                <Field>
                                    <FieldLabel htmlFor="away_team_id">Gastverein</FieldLabel>
                                    <SingleSelector
                                        value={selectedAwayTeam}
                                        onChange={opts => {
                                            setSelectedAwayTeam(opts);
                                            setData('away_team_id', opts[0]?.value ?? '');
                                        }}
                                        defaultOptions={clubOptions}
                                        groupBy="group"
                                        placeholder="Gastverein wählen"
                                        hidePlaceholderWhenSelected
                                        emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                                    />
                                    <InputError message={errors.home_team_id} />
                                </Field>
                                <Field>
                                    <DateTimePicker
                                        dateLabel="Datum"
                                        dateName="kickoff_date"
                                        dateErrorMessage={errors.kickoff_date}
                                        dateOnChange={(val) => setData('kickoff_date', val)}

                                        timeLabel="Zeit"
                                        timeName="kickoff_time"
                                        timeErrorMessage={errors.kickoff_time}
                                        timeOnChange={(val) => setData('kickoff_time', val)}
                                    />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </div>

                    <form onSubmit={submit} id="evaluation-from" className="flex flex-1 flex-col gap-4">
                        {evaluationCriteriaGroups.length <= 0 && <p className="p-5">Keine Bewertungskriterien gefunden</p>}
                        {evaluationCriteriaGroups.map(group => (
                            <div key={group.id} className="grid gap-y-4 relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                                <FieldSet>
                                    <FieldLegend>{group.name}</FieldLegend>
                                    <FieldGroup className="grid sm:grid-cols-2 gap-x-15">
                                        {group.evaluation_criteria.map(criteria => {
                                            const flatIndex = flatCriteria.findIndex(c => c.id === criteria.id);
                                            return (
                                                <Field key={criteria.id}>
                                                    <div className="flex flex-row justify-between">
                                                        <FieldLabel htmlFor={'criteria_' + criteria.id}>{criteria.name}</FieldLabel>
                                                        <FieldDescription>x{criteria.multiplier}</FieldDescription>
                                                    </div>
                                                    <ScoreBar name={'criteria_' + criteria.id} value={data.criteriaScores[criteria.id] ?? 0} onChange={val => setData('criteriaScores', {...data.criteriaScores, [criteria.id]: val})} />
                                                    <InputError message={(errors as Record<string, string>)[`criteriaScores.${flatIndex}.score`] ?? ''} />
                                                </Field>
                                            );
                                        })}
                                    </FieldGroup>
                                </FieldSet>
                            </div>
                        ))}

                        <div className="grid gap-y-4 relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                            <FieldSet>
                                <FieldLegend>Sonstiges</FieldLegend>
                                <FieldGroup>
                                    <Field>
                                        <FieldLabel htmlFor="strengths">Stärken</FieldLabel>
                                        <Textarea id="strengths"
                                            onChange={e => setData('strengths', e.target.value)}
                                            placeholder="Stärken eintragen"
                                            value={data.strengths}
                                        />
                                        <InputError message={errors.strengths} />
                                    </Field>
                                    <Field>
                                        <FieldLabel htmlFor="weaknesses">Schwächen</FieldLabel>
                                        <Textarea id="weaknesses"
                                            onChange={e => setData('weaknesses', e.target.value)}
                                            placeholder="Schwächen eintragen"
                                            value={data.weaknesses}
                                        />
                                        <InputError message={errors.weaknesses}/>
                                    </Field>
                                    <Field>
                                        <FieldLabel>Empfehlung</FieldLabel>
                                        <SingleSelector
                                            value={selectedRecommendation}
                                            onChange={opts => {
                                                setSelectedRecommendation(opts);
                                                setData('recommendation_id', opts[0]?.value ?? '');
                                            }}
                                            defaultOptions={recommendationOptions}
                                            groupBy="group"
                                            placeholder="Empfehlung wählen"
                                            hidePlaceholderWhenSelected
                                            emptyIndicator={<p className="text-center text-sm">Keine Empfehlung gefunden</p>}
                                        />
                                        <InputError message={errors.recommendation_id}/>
                                    </Field>
                                    <Field>
                                        <FieldLabel htmlFor="comment">Bemerkung</FieldLabel>
                                        <Textarea id="comment"
                                            onChange={e => setData('comment', e.target.value)}
                                            placeholder="Bemerkung eintragen"
                                            value={data.comment}
                                        />
                                        <InputError message={errors.comment}/>
                                    </Field>
                                </FieldGroup>
                            </FieldSet>
                        </div>

                        <Input type="hidden" name="player_id" value={selectedPlayer?.id ?? data.player_id} onChange={(val) => setData('player_id', String(val))}/>
                    </form>

                    <div>
                        <Button type="submit" form="evaluation-from" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                        {edit && backHref && (
                            <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>
                                Zurück
                            </Button>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
