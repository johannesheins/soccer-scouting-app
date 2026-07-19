import { useState } from "react";
import { Input } from "@/components/ui/input";
import { Slider } from "@/components/ui/slider";
import { cn } from "@/lib/utils";

type DefaultProps = {
    name?: string,
    className?: string,
    value: number,
    onChange?: (value: number) => void
    disabled?: boolean
}

type RangeProps = {
    nameFrom?: string,
    nameTo?: string,
    className?: string,
    valueFrom: number,
    valueTo: number,
    onChangeFrom?: (value: number) => void
    onChangeTo?: (value: number) => void
    disabled?: boolean
}

export function ScoreBar({name, value, onChange, className, disabled = false}: DefaultProps) {
    const [currentValue, setCurrentValue] = useState(value);

    function update(next: number[]) {
        setCurrentValue(next[0]);
        onChange?.(next[0]);
    }

    return (
        <div className={cn(className, "flex items-center gap-3")}>
            <Slider
                className="flex-1"
                value={[currentValue]}
                onValueChange={update}
                min={0}
                max={10}
                step={1}
                disabled={disabled}
            />
            <span className="w-10 text-right text-sm tabular-nums text-foreground">
                {currentValue}
            </span>
            <Input type="hidden" name={name} value={currentValue} min="0" max="10" readOnly />
        </div>
    )
}

export function ScoreBarRange({nameFrom, nameTo, valueFrom, valueTo, onChangeFrom, onChangeTo, className, disabled = false}: RangeProps) {
    const [currentValueFrom, setCurrentValueFrom] = useState(valueFrom);
    const [currentValueTo, setCurrentValueTo] = useState(valueTo);

    function update(next: number[]) {
        setCurrentValueFrom(next[0]);
        setCurrentValueTo(next[1]);
        onChangeFrom?.(next[0]);
        onChangeTo?.(next[1]);
    }

    return (
        <div className={cn(className, "flex items-center gap-3")}>
            <Slider
                className="flex-1"
                value={[currentValueFrom, currentValueTo]}
                onValueChange={update}
                min={0}
                max={10}
                step={1}
                disabled={disabled}
            />
            <span className="w-10 text-right text-sm tabular-nums text-muted-foreground">
                {[currentValueFrom, currentValueTo].join(', ')}
            </span>
            <Input type="hidden" name={nameFrom} value={currentValueFrom} min="0" max="10" readOnly />
            <Input type="hidden" name={nameTo} value={currentValueTo} min="0" max="10" readOnly />
        </div>
    )
}
