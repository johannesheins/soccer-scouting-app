import * as React from "react"

import { Field, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import InputError from "@/components/input-error"

type Props = { name: string; errorMessage?: string; label: string }
export function TimeInput({ name, errorMessage, label }: Props) {
    const minutesRef = React.useRef<HTMLInputElement>(null)
    const [hours, setHours] = React.useState("10")
    const [minutes, setMinutes] = React.useState("30")
    const pad = (v: string) => v.padStart(2, "0")

    return (
        <Field className="w-auto">
            <FieldLabel htmlFor="time-hours">{label}</FieldLabel>
            <div className="flex border-input shadow-xs h-9 items-center rounded-md border px-2 transition-[color,box-shadow] focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/50">
                <Input
                    id="time-hours"
                    type="number"
                    min={0}
                    max={23}
                    value={hours}
                    onChange={e => setHours(e.target.value)}
                    className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    onInput={(e) => {
                        const val = e.currentTarget.value;
                        if (val.length >= 2 || Number(val) > 2) {
                            pad(val);
                            minutesRef.current?.focus();
                            minutesRef.current?.select();
                        }
                    }}
                    onBlur={(e) => setHours(pad(e.target.value))}
                    onClick={(e) => e.currentTarget.select()}
                />
                <span className="text-sm font-medium">:</span>
                <Input
                    type="number"
                    min={0}
                    max={59}
                    value={minutes}
                    onChange={e => setMinutes(e.target.value)}
                    onBlur={(e) => setMinutes(pad(e.target.value))}
                    className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    onClick={(e) => e.currentTarget.select()}
                    ref={minutesRef}
                />
            </div>
            <Input type="hidden" name={name} value={`${pad(hours)}:${pad(minutes)}`} />
            <InputError message={errorMessage}/>
        </Field>
    )
}
