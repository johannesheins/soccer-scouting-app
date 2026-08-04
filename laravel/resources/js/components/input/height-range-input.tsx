import {Field} from "@/components/ui/field";
import React from "react";
import HeightInput from "@/components/input/height-input";

type Props = {
    nameFrom: string,
    nameTo: string,
    valueFrom: string,
    valueTo: string,
    setData: (key: string, value: string) => void,
    errorFrom?: string,
    errorTo?: string,
}

export default function HeightRangeInput({nameFrom, nameTo, valueFrom, valueTo, setData, errorFrom, errorTo}: Props){
    return (
        <>
            <Field>
                <HeightInput name={nameFrom} label="Größe von" value={valueFrom} setData={setData} error={errorFrom}/>
            </Field>
            <Field>
                <HeightInput name={nameTo} label="Größe bis" value={valueTo} setData={setData} error={errorTo}/>
            </Field>
        </>
    )
}
