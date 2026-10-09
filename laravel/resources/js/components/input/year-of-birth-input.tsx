import React, {useState} from "react";
import InputError from "@/components/input-error";
import {FieldLabel} from "@/components/ui/field";
import MultipleSelector from "@/components/ui/multi-select";
import type {Option} from "@/components/ui/multi-select";
import {SingleSelector} from "@/components/ui/single-select";
import {getYearOptions} from "@/hooks/form-options";
import { t } from '@/locale/translate';

type Props = {
    variant: "multiple" | "single",
    name: string,
    setData: (key: string, values: number[]|number) => void,
    selectedValues: number[],
    error?: string,
    maxSelected?: number;
}

export default function YearOfBirthInput({variant = "single", name, selectedValues, setData, error, maxSelected}: Props){
    const yearOfBirthOptions = getYearOptions();
    const [selectedYearOfBirth, setSelectedYearOfBirth] = useState<Option[]>(
        yearOfBirthOptions.filter(o => selectedValues?.includes(Number(o.value)))
    );

    return (
        <>
            <FieldLabel htmlFor={name}>{t('Year of birth')}</FieldLabel>
            {variant === "multiple" ? (
                <MultipleSelector
                    value={selectedYearOfBirth}
                    onChange={opts => {
                        setSelectedYearOfBirth(opts);
                        setData(name, opts.map(o => Number(o.value)));
                    }}
                    defaultOptions={yearOfBirthOptions}
                    placeholder={t('Select year of birth')}
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">{t('No year of birth found')}</p>}
                    maxSelected={maxSelected}
                />
            ) : (
                <SingleSelector
                    value={selectedYearOfBirth}
                    onChange={opts => {
                        setSelectedYearOfBirth(opts);
                        setData(name, Number(opts[0]?.value) ?? '');
                    }}
                    defaultOptions={yearOfBirthOptions}
                    placeholder={t('Select year of birth')}
                    hidePlaceholderWhenSelected
                    emptyIndicator={<p className="text-center text-sm">{t('No year of birth found')}</p>}
                />
            )}
            <InputError message={error} />
        </>
    )
}
