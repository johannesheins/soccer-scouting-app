import React, {useState} from "react";
import {Field, FieldDescription, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
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

type Props = {
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    players: PlayerOption[];
    clubs: Club[],
    queryParams: EvaluationSearchQuery,
};
export default function EvaluationSearchForm({evaluationCriteriaGroups, players, queryParams}: Props){
    const playerOptions = toPlayerOptions(players);

    const [selectedPlayers, setSelectedPlayers] = useState(
        playerOptions.filter(o => queryParams?.player_ids?.includes(Number(o.value)))
    );

    const { data, setData, processing, errors } = useForm({
        player_ids: queryParams.player_ids ?? [],
        criteria_scores_from: queryParams.criteria_scores_from ?? [],
        criteria_scores_to: queryParams.criteria_scores_to ?? [],
        open_accordion: queryParams.open_accordion ?? [],
    });

    const flatCriteria = evaluationCriteriaGroups.flatMap(g => g.evaluation_criteria);

    async function submit(e: React.SubmitEvent){
        e.preventDefault()
        const jsonData = JSON.stringify(data);
        const base64Encoded = btoa(jsonData);
        return router.get(evaluation.search.url()+'/'+base64Encoded);
    }

    function resetForm(){
        router.get(evaluation.search.url());
    }

    function toggle(id: number){
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
                    <FieldSet>
                        <FieldGroup>
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
                        </FieldGroup>

                        <FieldGroup className="gap-4">
                            {evaluationCriteriaGroups.length <= 0 && <p className="p-5">Keine Bewertungskriterien gefunden</p>}
                            {evaluationCriteriaGroups.map(group => (
                                <Accordion defaultValue={openAccordions} key={group.id} type="multiple" className="grid px-2 relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                                    <AccordionItem value={String(group.id)} >
                                        <AccordionTrigger onClick={() => toggle(group.id)}>{group.name}</AccordionTrigger>
                                        <AccordionContent>
                                            <FieldGroup className="grid sm:grid-cols-2 gap-x-15">
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
                                </Accordion>
                            ))}
                        </FieldGroup>
                        <Field className="w-fit flex flex-row">
                            <Button type="submit" disabled={processing}>Suchen</Button>
                            <Button type="button" variant="secondary" onClick={resetForm}>Zurücksetzen</Button>
                        </Field>
                    </FieldSet>
                </form>
            </div>
        </>
    )
}
