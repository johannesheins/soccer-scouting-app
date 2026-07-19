import React from "react";
import {Field, FieldDescription, FieldGroup, FieldLabel, FieldSet} from "@/components/ui/field";
import {router, useForm} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Club, EvaluationCriteriaGroups} from "@/types/types";
import {ScoreBarRange} from "@/components/score-bar";
import {useUrlParamBracket} from "@/hooks/useUrlParam";
import InputError from "@/components/input-error";
import evaluation from "@/routes/evaluation";
import {Accordion, AccordionContent, AccordionItem, AccordionTrigger} from "@/components/ui/accordion";

type Props = {
    evaluationCriteriaGroups: EvaluationCriteriaGroups[],
    clubs: Club[]
};
export default function EvaluationSearchForm({evaluationCriteriaGroups}: Props){
    const { data, setData, get, processing, errors } = useForm({
        criteria_scores_from: (useUrlParamBracket('criteria_scores_from') as string[]).map(score => Number(score)),
        criteria_scores_to: (useUrlParamBracket('criteria_scores_to') as string[]).map(score => Number(score)),
    });
    console.log(data.criteria_scores_from, useUrlParamBracket('criteria_scores_to'));
    console.log(data.criteria_scores_to, useUrlParamBracket('criteria_scores_to'));

    const flatCriteria = evaluationCriteriaGroups.flatMap(g => g.evaluation_criteria);

    async function submit(e: React.SubmitEvent){
        e.preventDefault()
        return get(evaluation.search.url());
    }

    function resetForm(){
        router.get(window.location.pathname);
    }

    return (
        <>
            <div>
                <form onSubmit={submit}>
                    <FieldSet>
                        <FieldGroup className="gap-4">
                        {evaluationCriteriaGroups.length <= 0 && <p className="p-5">Keine Bewertungskriterien gefunden</p>}
                        {evaluationCriteriaGroups.map(group => (
                            <Accordion key={group.id} type="multiple" className="grid px-2 relative rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                                <AccordionItem value={String(group.id)} >
                                    <AccordionTrigger>{group.name}</AccordionTrigger>
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
