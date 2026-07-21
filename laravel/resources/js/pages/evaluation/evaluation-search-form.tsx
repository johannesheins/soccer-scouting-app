import React, {useState} from "react";
import {Field, FieldDescription, FieldGroup, FieldLabel} from "@/components/ui/field";
import {router, useForm} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Club, EvaluationCriteriaGroups, EvaluationSearchQuery, PlayerOption} from "@/types/types";
import {ScoreBarRange} from "@/components/score-bar";
import InputError from "@/components/input-error";
import evaluation from "@/routes/evaluation";
import {Accordion, AccordionContent, AccordionItem, AccordionTrigger} from "@/components/ui/accordion";
import MultipleSelector from "@/components/ui/multi-select";
import {toPlayerOptions} from "@/hooks/form-options";
import {evaluationSearchRequest} from "@/request/evaluation-search-request";
import {Tabs, TabsContent, TabsList, TabsTrigger} from "@/components/ui/tabs";
import ClubInput from "@/components/input/club-input";
import YearOfBirthInput from "@/components/input/year-of-birth-input";

type Props = {
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    players: PlayerOption[];
    clubs: Club[],
    queryParams: EvaluationSearchQuery,
};
export default function EvaluationSearchForm({evaluationCriteriaGroups, players, clubs, queryParams}: Props){
    const playerOptions = toPlayerOptions(players);
    const [selectedPlayers, setSelectedPlayers] = useState(
        playerOptions.filter(o => queryParams?.player_ids?.includes(Number(o.value)))
    );

    const flatCriteria = evaluationCriteriaGroups.flatMap(g => g.evaluation_criteria);

    const { data, setData, processing, errors } = useForm({
        criteria_scores_from: queryParams.criteria_scores_from ?? [],
        criteria_scores_to: queryParams.criteria_scores_to ?? [],

        player_ids: queryParams.player_ids ?? [],
        club_ids: queryParams.club_ids ?? [],
        years_of_birth: queryParams.years_of_birth ?? [],

        open_tab: queryParams.open_tab ?? 'criteria',
        open_accordion: queryParams.open_accordion ?? (evaluationCriteriaGroups[0] ? {[evaluationCriteriaGroups[0].id]: true} : {}),
    });

    async function submit(e: React.SubmitEvent){
        e.preventDefault()
        return evaluationSearchRequest(data);
    }

    function resetForm(){
        router.get(evaluation.search.url());
    }

    function setOpenTab(tab: string){
        setData('open_tab', tab);
    }

    function toggleAccordionState(id: number){
        const val = !data.open_accordion[id];
        setData('open_accordion', {...data.open_accordion, [id]: val});
    }

    const openAccordions = Object.entries(data.open_accordion)
        .filter(([, state]) => Boolean(Number(state)))
        .map(([id]) => id);

    return (
        <>
            <div>
                <form onSubmit={submit}>
                    <Tabs defaultValue={data.open_tab}>
                        <TabsList>
                            <TabsTrigger value="criteria" onClick={() =>setOpenTab('criteria')}>
                                Kriterien
                            </TabsTrigger>
                            <TabsTrigger value="player" onClick={() =>setOpenTab('player')}>
                                Spieler
                            </TabsTrigger>
                        </TabsList>

                        <TabsContent value="criteria">
                            <Accordion defaultValue={openAccordions} type="multiple">
                                {evaluationCriteriaGroups.length <= 0 && <p className="p-5">Keine Bewertungskriterien gefunden</p>}
                                {evaluationCriteriaGroups.map(group => (
                                    <AccordionItem key={group.id} value={String(group.id)} >
                                        <AccordionTrigger onClick={() => toggleAccordionState(group.id)}>{group.name}</AccordionTrigger>
                                        <AccordionContent>
                                            <FieldGroup className="grid sm:grid-cols-2 xl:grid-cols-3 gap-x-15">
                                                {group.evaluation_criteria.map(criteria => {
                                                    const flatIndex = flatCriteria.findIndex(c => c.id === criteria.id);
                                                    return (
                                                        <Field key={criteria.id}>
                                                            <div className="flex flex-row justify-between">
                                                                <FieldLabel htmlFor={'criteria_' + criteria.id}>{criteria.name}</FieldLabel>
                                                                <FieldDescription>x{criteria.multiplier}</FieldDescription>
                                                            </div>
                                                            <ScoreBarRange
                                                                nameFrom={'criteria_from' + criteria.id}
                                                                nameTo={'criteria_to' + criteria.id}
                                                                valueFrom={data.criteria_scores_from[criteria.id] ?? 0}
                                                                valueTo={data.criteria_scores_to[criteria.id] ?? 10}
                                                                onChangeFrom={val => setData('criteria_scores_from', {...data.criteria_scores_from, [criteria.id]: val})}
                                                                onChangeTo={val => setData('criteria_scores_to', {...data.criteria_scores_to, [criteria.id]: val})}
                                                            />
                                                            <InputError message={(errors as Record<string, string>)[`criteriaScores.${flatIndex}.score`] ?? ''} />
                                                        </Field>
                                                    );
                                                })}
                                            </FieldGroup>
                                        </AccordionContent>
                                    </AccordionItem>
                                ))}
                            </Accordion>
                        </TabsContent>

                        <TabsContent value="player">
                            <FieldGroup className="grid sm:grid-cols-2 xl:grid-cols-3">
                                <Field>
                                    <FieldLabel>Spieler</FieldLabel>
                                    <MultipleSelector
                                        value={selectedPlayers}
                                        onChange={opts => {
                                            setSelectedPlayers(opts);
                                            setData('player_ids', opts.map(o => Number(o.value)));
                                        }}
                                        defaultOptions={playerOptions}
                                        groupBy="group"
                                        placeholder="Spieler wählen"
                                        hidePlaceholderWhenSelected
                                        emptyIndicator={<p className="text-center text-sm">Keine Spieler gefunden</p>}
                                    />
                                    <InputError message={errors.player_ids}/>
                                </Field>
                                <Field>
                                    <ClubInput variant="multiple" name="club_ids" clubs={clubs} setData={setData} selectedValues={queryParams.club_ids} error={errors.club_ids}/>
                                </Field>
                                <Field>
                                    <YearOfBirthInput variant="multiple" name="years_of_birth" setData={setData} selectedValues={queryParams?.years_of_birth} error={errors.years_of_birth} />
                                </Field>
                            </FieldGroup>
                        </TabsContent>
                        <Field className="w-fit flex flex-row mt-4">
                            <Button type="submit" disabled={processing}>Suchen</Button>
                            <Button type="button" variant="secondary" onClick={resetForm}>Zurücksetzen</Button>
                        </Field>
                    </Tabs>
                    <FieldGroup>
                    </FieldGroup>
                </form>
            </div>
        </>
    )
}
