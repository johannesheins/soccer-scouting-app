import {FieldLabel} from "@/components/ui/field";
import MultipleSelector, {type Option} from "@/components/ui/multi-select";
import InputError from "@/components/input-error";
import React, {useState} from "react";
import {getYearOptions} from "@/hooks/form-options";
import {SingleSelector} from "@/components/ui/single-select";

type Props = {
    variant: "multiple" | "single",
    name: string,
    setData: (key: string, values: number[]|number) => void,
    selectedValues: number[],
    error?: string
}

export default function YearOfBirthInput({variant = "single", name, selectedValues, setData, error}: Props){
    const yearOfBirthOptions = getYearOptions();
    const [selectedYearOfBirth, setSelectedYearOfBirth] = useState<Option[]>(
        yearOfBirthOptions.filter(o => selectedValues?.includes(Number(o.value)))
    );

    return (
        <>
            <FieldLabel htmlFor={name}>Jahrgang</FieldLabel>
            {variant === "multiple" ? (
                <MultipleSelector
                    value={selectedYearOfBirth}
                    onChange={opts => {
                        setSelectedYearOfBirth(opts);
                        setData(name, opts.map(o => Number(o.value)));
                    }}
                    defaultOptions={yearOfBirthOptions}
                    placeholder="Jahrgang wählen"
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">Keinen Jahrgang gefunden</p>}
                />
            ) : (
                <SingleSelector
                    value={selectedYearOfBirth}
                    onChange={opts => {
                        setSelectedYearOfBirth(opts);
                        setData(name, Number(opts[0]?.value) ?? '');
                    }}
                    defaultOptions={yearOfBirthOptions}
                    placeholder="Jahrgang wählen"
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">Keinen Jahrgang gefunden</p>}
                />
            )}
            <InputError message={error} />
        </>
    )
}
