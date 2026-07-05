import * as React from "react"

import { Field, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import InputError from "@/components/input-error"
import {cn} from "@/lib/utils";

type Props = { name: string; errorMessage?: string; label?: string; onChange?: (value: string) => void }
export function TimeInput({ name, errorMessage, label, onChange }: Props) {
    const hoursRef = React.useRef<HTMLInputElement>(null);
    const minutesRef = React.useRef<HTMLInputElement>(null);

    const isPlaceholder = Number(hoursRef.current?.value) === 0 && Number(minutesRef.current?.value) === 0

    const [hours, setHours] = React.useState('')
    const [minutes, setMinutes] = React.useState('')
    const pad = (v: string) => v.padStart(2, "0")

    const isFirstRender = React.useRef(true)
    React.useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false
            return
        }
        onChange?.(`${pad(hours)}:${pad(minutes)}`)
    }, [hours, minutes])

    return (
        <Field className="w-auto">
            {label && <FieldLabel htmlFor="time-hours">{label}</FieldLabel>}
            <div className="flex border-input shadow-xs h-9 items-center rounded-md border px-2 transition-[color,box-shadow] focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/50">
                <Input
                    id="time-hours"
                    type="number"
                    min={0}
                    max={23}
                    placeholder='10'
                    value={hours}
                    onChange={e => setHours(e.target.value)}
                    className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    ref={hoursRef}
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
                <span className={cn("text-sm font-medium", isPlaceholder ? 'text-muted-foreground' : '')}>:</span>
                <Input
                    type="number"
                    min={0}
                    max={59}
                    placeholder='30'
                    value={minutes}
                    onChange={e => setMinutes(e.target.value)}
                    onBlur={(e) => setMinutes(pad(e.target.value))}
                    className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    onClick={(e) => e.currentTarget.select()}
                    ref={minutesRef}
                />
            </div>
            <Input type="hidden" name={name} value={`${pad(hours)}:${pad(minutes)}`} readOnly/>
            <InputError message={errorMessage}/>
        </Field>
    )
}
