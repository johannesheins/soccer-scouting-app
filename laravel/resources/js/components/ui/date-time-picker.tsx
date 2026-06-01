"use client"

import * as React from "react"
import { format } from "date-fns"
import { de } from "date-fns/locale"
import { ChevronDownIcon } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover"
import InputError from "@/components/input-error";

type Props = { dateLabel: string, dateName: string, dateErrorMessage?: string, timeLabel: string, timeName: string, timeErrorMessage?: string }
export function DateTimePicker({ dateLabel, dateName, dateErrorMessage, timeName, timeErrorMessage, timeLabel }: Props) {
    const [open, setOpen] = React.useState(false)
    const [date, setDate] = React.useState<Date | undefined>(undefined)
    const [hours, setHours] = React.useState("10")
    const [minutes, setMinutes] = React.useState("30")
    const pad = (v: string) => v.padStart(2, "0")

    return (
        <FieldGroup className="max-w-xs flex-row">
            <Field>
                <FieldLabel htmlFor="date-picker-optional">{dateLabel}</FieldLabel>
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                        <Button
                            variant="outline"
                            id="date-picker-optional"
                            className="w-32 justify-between font-normal"
                        >
                            {date ? format(date, "dd.MM.yyyy", { locale: de }) : "Datum wählen"}
                            <ChevronDownIcon />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                        <Calendar
                            mode="single"
                            selected={date}
                            captionLayout="dropdown"
                            defaultMonth={date}
                            locale={de}
                            onSelect={(date) => {
                                setDate(date)
                                setOpen(false)
                            }}
                            timeZone="Europe/Berlin"
                        />
                    </PopoverContent>
                </Popover>
                <Input type="hidden" name={dateName} value={String(date)}/>
                <InputError message={dateErrorMessage}/>
            </Field>
            <Field className="w-auto">
                <FieldLabel htmlFor="time-hours">{timeLabel}</FieldLabel>
                <div className="flex border-input shadow-xs h-9 items-center rounded-md border px-2 transition-[color,box-shadow] focus-within:border-ring focus-within:ring-[3px] focus-within:ring-ring/50">
                    <Input
                        id="time-hours"
                        type="number"
                        min={0}
                        max={23}
                        value={hours}
                        onChange={e => setHours(e.target.value)}
                        className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    />
                    <span className="text-sm font-medium">:</span>
                    <Input
                        type="number"
                        min={0}
                        max={59}
                        value={minutes}
                        onChange={e => setMinutes(e.target.value)}
                        className="w-7 border-0 p-0 text-center shadow-none focus-visible:ring-0"
                    />
                </div>
                <Input type="hidden" name={timeName} value={`${pad(hours)}:${pad(minutes)}`} />
                <InputError message={timeErrorMessage}/>
            </Field>
        </FieldGroup>
    )
}
