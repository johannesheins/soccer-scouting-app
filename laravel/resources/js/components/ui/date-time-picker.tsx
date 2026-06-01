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
import { TimeInput } from "@/components/ui/time-input"

type Props = { dateLabel: string, dateName: string, dateErrorMessage?: string, timeLabel: string, timeName: string, timeErrorMessage?: string }
export function DateTimePicker({ dateLabel, dateName, dateErrorMessage, timeName, timeErrorMessage, timeLabel }: Props) {
    const [open, setOpen] = React.useState(false)
    const [date, setDate] = React.useState<Date | undefined>(undefined)

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
            <TimeInput name={timeName} errorMessage={timeErrorMessage} label={timeLabel} />
        </FieldGroup>
    )
}
