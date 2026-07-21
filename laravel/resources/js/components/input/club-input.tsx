import {FieldLabel} from "@/components/ui/field";
import MultipleSelector, {type Option} from "@/components/ui/multi-select";
import InputError from "@/components/input-error";
import React, {useState} from "react";
import {toClubOptions} from "@/hooks/form-options";
import {Club} from "@/types/types";
import {SingleSelector} from "@/components/ui/single-select";

type Props = {
    variant: "multiple" | "single",
    name: string,
    clubs: Club[],
    setData: (key: string, values: number[]|number) => void,
    selectedValues: number[],
    error?: string
}

export default function ClubInput({variant = "single", name, clubs, selectedValues, setData, error}: Props){
    const clubOptions = toClubOptions(clubs);
    const [selectedClub, setSelectedClub] = useState<Option[]>(
        clubOptions.filter(o => selectedValues?.includes(Number(o.value)))
    );

    return (
        <>
            <FieldLabel htmlFor={name}>Club</FieldLabel>
            {variant === "multiple" ? (
                <MultipleSelector
                    value={selectedClub}
                    onChange={opts => {
                        setSelectedClub(opts);
                        setData(name, opts.map(o => Number(o.value)));
                    }}
                    defaultOptions={clubOptions}
                    groupBy="group"
                    placeholder="Verein wählen"
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                />
            ) : (
                <SingleSelector
                    value={selectedClub}
                    onChange={opts => {
                        setSelectedClub(opts);
                        setData(name, Number(opts[0]?.value) ?? '');
                    }}
                    defaultOptions={clubOptions}
                    groupBy="group"
                    placeholder="Verein wählen"
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">Keinen Verein gefunden</p>}
                />
            )}
            <InputError message={error} />
        </>
    )
}
