import {Head, router, useForm, usePage} from '@inertiajs/react';
import React, {useEffect, useState} from 'react';
import InputError from "@/components/input-error";
import {MAX_SCORE, ScoreBar} from "@/components/score-bar";
import ScoreDisplay from "@/components/score-display";
import {Button} from "@/components/ui/button";
import {DatePicker} from "@/components/ui/date-picker";
import {
    Field,
    FieldGroup,
    FieldLabel,
    FieldLegend,
    FieldSet,
} from "@/components/ui/field"
import {Input} from "@/components/ui/input";
import {SingleSelector} from "@/components/ui/single-select";
import {Textarea} from "@/components/ui/textarea";
import {PlayerRequestNameEnum as Name} from "@/enums";
import {toClubOptions, toRecommendationOptions} from "@/hooks/form-options";
import PlayerSearchDialog from "@/pages/player/player-search-dialog";
import {ScoreCalculationService} from "@/services/score-calculation-service";
import type {Club} from "@/types/club";
import {EvaluationRoutes, EvaluationSmallType, EvaluationSmallTypeMap} from "@/types/evaluation/evaluation";
import type {EvaluationCriteriaGroups} from "@/types/evaluation-criteria";
import type {Player} from "@/types/player";
import type {Position} from "@/types/position";
import type {Recommendation} from "@/types/recommendation";
import {GameEvaluationSmall} from "@/types/evaluation/game-evaluation";
import {PlayerEvaluationSmall} from "@/types/evaluation/player-evaluation";


export default function EvaluationForm<T extends  EvaluationSmallType>({ type, route, edit = false, backHref = null }: {
    type: T,
    route: EvaluationRoutes
    edit?: boolean,
    backHref?: string | null
}){
    type Props = {
        evaluation?: EvaluationSmallTypeMap[T],
        evaluationCriteriaGroups: EvaluationCriteriaGroups[],
        positions: Position[];
        clubs: Club[],
        recommendations: Recommendation[],
        player: Player,
    };

    const isGameEvaluation = type === 'game';
    const isPlayerEvaluation = type === 'player';

    const { evaluation, evaluationCriteriaGroups, positions, clubs, recommendations, player } = usePage<Props>().props;

    const gameEvaluation = isGameEvaluation ? (evaluation as GameEvaluationSmall | undefined) : undefined;
    const playerEvaluation = isPlayerEvaluation ? (evaluation as PlayerEvaluationSmall | undefined) : undefined;

    const [selectedPlayer, setSelectedPlayer] = useState<Player>(player);
    useEffect(() => {
        setData(Name.playerId, String(selectedPlayer?.id) ?? '');
    }, [selectedPlayer]);

    const clubOptions = toClubOptions(clubs);
    const [selectedHomeClub, setSelectedHomeClub] = useState(
        clubOptions.filter(o => o.value === String(gameEvaluation?.home_club_id))
    );
    const [selectedGuestClub, setSelectedGuestClub] = useState(
        clubOptions.filter(o => o.value === String(gameEvaluation?.guest_club_id))
    );

    const recommendationOptions = toRecommendationOptions(recommendations);
    const [selectedRecommendation, setSelectedRecommendation] = useState(
        recommendationOptions.filter(o => o.value === String(evaluation?.recommendation_id))
    );

    const { data, setData, transform, post, put, processing, errors } = useForm({
        ...((isGameEvaluation || isPlayerEvaluation) && {
            [Name.playerId]: String(gameEvaluation?.player_id ?? playerEvaluation?.player_id ?? ''),
        }),
        ...(isGameEvaluation && {
            home_club_id: gameEvaluation?.home_club_id ?? '',
            home_team: gameEvaluation?.home_team ?? '',
            guest_club_id: gameEvaluation?.guest_club_id ?? '',
            guest_team: gameEvaluation?.guest_team ?? '',
        }),
        date: evaluation?.date ?? '',
        strengths: evaluation?.strengths ?? '',
        weaknesses: evaluation?.weaknesses ?? '',
        recommendation_id: evaluation?.recommendation_id ?? '',
        comment: evaluation?.comment,
        criteriaScores: Object.fromEntries(
            (evaluation?.criteria_scores ?? []).map(s => [s.evaluation_criteria_id, s.score])
        ) as Record<number, number>,
    });

    const calculateScores = new ScoreCalculationService(data.criteriaScores, evaluationCriteriaGroups);

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
            return put(route.update.url(evaluation.id));
        }
        return post(route.store.url());
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
                                    <PlayerSearchDialog positions={positions} clubs={clubs} selectPlayer={true} value={player} onSelectedPlayer={setSelectedPlayer}/>
                                    <InputError message={errors[Name.playerId]} />
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </div>

                    <div className="relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                        <FieldSet>
                            <FieldLegend>Spieldaten</FieldLegend>
                            <FieldGroup className="grid grid-cols-2 gap-4">
                                {isGameEvaluation && (
                                    <>
                                        <FieldGroup className="grid grid-cols-[3fr_1fr] gap-4">
                                            <Field>
                                                <FieldLabel htmlFor="home_club_id">Heimverein</FieldLabel>
                                                <SingleSelector
                                                    value={selectedHomeClub}
                                                    onChange={opts => {
                                                        setSelectedHomeClub(opts);
                                                        setData('home_club_id', opts[0]?.value ?? '');
                                                    }}
                                                    defaultOptions={clubOptions}
                                                    groupBy="group"
                                                    placeholder="Heimmverein wählen"
                                                    hidePlaceholderWhenSelected
                                                    emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                                                />
                                                <InputError message={errors.home_club_id} />
                                            </Field>
                                            <Field>
                                                <FieldLabel htmlFor="home_team">Mannschaft</FieldLabel>
                                                <Input
                                                    type="text"
                                                    name="home_team"
                                                    id="home_team"
                                                    value={data.home_team}
                                                    onChange={e => setData('home_team', e.target.value)}
                                                    placeholder="U19 III"
                                                    maxLength={7}
                                                />
                                                <InputError message={errors.home_team} />
                                            </Field>
                                        </FieldGroup>
                                        <FieldGroup className="grid grid-cols-[3fr_1fr] gap-4">
                                            <Field>
                                                <FieldLabel htmlFor="guest_club_id">Gastverein</FieldLabel>
                                                <SingleSelector
                                                    value={selectedGuestClub}
                                                    onChange={opts => {
                                                        setSelectedGuestClub(opts);
                                                        setData('guest_club_id', opts[0]?.value ?? '');
                                                    }}
                                                    defaultOptions={clubOptions}
                                                    groupBy="group"
                                                    placeholder="Gastverein wählen"
                                                    hidePlaceholderWhenSelected
                                                    emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                                                />
                                                <InputError message={errors.guest_club_id} />
                                            </Field>
                                            <Field>
                                                <FieldLabel htmlFor="guest_team">Mannschaft</FieldLabel>
                                                <Input
                                                    type="text"
                                                    name="guest_team"
                                                    id="guest_team"
                                                    value={data.guest_team}
                                                    onChange={e => setData('guest_team', e.target.value)}
                                                    placeholder="U19 III"
                                                    maxLength={7}
                                                />
                                                <InputError message={errors.guest_team} />
                                            </Field>
                                        </FieldGroup>
                                    </>
                                )}

                                <Field>
                                    <DatePicker
                                        dateLabel="Datum"
                                        dateName="date"
                                        dateValue={evaluation?.date}
                                        dateErrorMessage={errors.date}
                                        dateOnChange={(val) => setData('date', val)}
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
                                    <FieldLegend className="flex justify-between w-full">
                                        <span>{group.name}</span>
                                        <ScoreDisplay currentValue={calculateScores.getGroupScore(group.id)} maxScore={MAX_SCORE * group.evaluation_criteria.length} />
                                    </FieldLegend>
                                    <FieldGroup className="grid sm:grid-cols-2 gap-x-15">
                                        {group.evaluation_criteria.map(criteria => {
                                            const flatIndex = flatCriteria.findIndex(c => c.id === criteria.id);
                                            return (
                                                <Field key={criteria.id}>
                                                    <FieldLabel htmlFor={'criteria_' + criteria.id}>{criteria.name}</FieldLabel>
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

                        <Input type="hidden" name={Name.playerId} value={player?.id ?? selectedPlayer?.id ?? data[Name.playerId] ?? ''} onChange={(val) => setData(Name.playerId, String(val))}/>
                    </form>

                    <FieldGroup className="grid grid-cols-2">
                        <Field className="w-fit flex-row">
                            <Button type="submit" form="evaluation-from" disabled={processing}>{edit ? 'Aktualisieren' : 'Erstellen'}</Button>
                            {edit && backHref && (
                                <Button variant="secondary" type="button" onClick={() => router.get(backHref)}>
                                    Zurück
                                </Button>
                            )}
                        </Field>
                        <Field>
                            <span className="text-end font-medium">Gesamt: {calculateScores.getTotalScore()}</span>
                        </Field>
                    </FieldGroup>
                </div>
            </div>
        </>
    );
}
